<?php
// includes/auth_check.php
// Expt 7 — Session management + Remember Me cookie auto-login
// Include at the top of every .php page:
//   require_once 'includes/auth_check.php';
//
// Then call as needed:
//   requireLogin();          — any logged-in user
//   requireRole('seller');   — sellers only
//   requireRole('admin');    — admin only

if (session_status() === PHP_SESSION_NONE) session_start();

//  Expt 7: Auto-login via Remember Me cookie 
// If not logged in but a valid remember_token cookie exists → restore session
if (!isset($_SESSION['user_id']) && !empty($_COOKIE['remember_token'])) {
    // Only attempt if db.php is already loaded (pages that include db.php)
    if (isset($pdo)) {
        _tryAutoLogin($pdo);
    }
}

function _tryAutoLogin(PDO $pdo): void {
    $token = $_COOKIE['remember_token'] ?? '';
    if (empty($token)) return;

    // Validate token format (must be 64 hex chars)
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        setcookie('remember_token', '', time() - 3600, '/');
        return;
    }

    try {
        $stmt = $pdo->prepare(
            'SELECT id, firstname, role, is_active FROM users
             WHERE remember_token = $1 AND is_active = TRUE LIMIT 1'
        );
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            // Valid token — restore the session
            session_regenerate_id(true);
            $_SESSION['user_id']   = (int) $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['role']      = $user['role'];

            // Rotate the token for security (rolling tokens)
            $newToken = bin2hex(random_bytes(32));
            $pdo->prepare('UPDATE users SET remember_token = $1 WHERE id = $2')
                ->execute([$newToken, $user['id']]);
            setcookie('remember_token', $newToken, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        } else {
            // Invalid token — clear the cookie
            setcookie('remember_token', '', time() - 3600, '/');
        }
    } catch (Exception $e) {
        error_log('Auto-login error: ' . $e->getMessage());
    }
}

//  Helper functions 
function requireLogin(string $redirectTo = '/login.php'): void {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: $redirectTo");
        exit;
    }
}

function requireRole(string $role): void {
    requireLogin();
    if (($_SESSION['role'] ?? '') !== $role) {
        http_response_code(403);
        echo '<!doctype html><html><head><title>Access Denied</title></head><body>
              <h2>403 — Access Denied</h2>
              <p>You do not have permission to view this page.</p>
              <a href="/index.php">← Go Home</a></body></html>';
        exit;
    }
}

function isLoggedIn(): bool  { return isset($_SESSION['user_id']); }
function isBuyer(): bool     { return ($_SESSION['role'] ?? '') === 'buyer';  }
function isSeller(): bool    { return ($_SESSION['role'] ?? '') === 'seller'; }
function isAdmin(): bool     { return ($_SESSION['role'] ?? '') === 'admin';  }
function userName(): string  { return htmlspecialchars($_SESSION['firstname'] ?? ''); }