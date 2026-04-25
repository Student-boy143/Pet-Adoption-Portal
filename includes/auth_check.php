<?php
// includes/auth_check.php
// Session-based authentication 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//  Require user to be logged in
function requireLogin(string $redirectTo = '/login.php'): void {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: $redirectTo");
        exit;
    }
}

// Require specific role (admin/seller/buyer)
function requireRole(string $role): void {
    requireLogin();

    if (($_SESSION['role'] ?? '') !== $role) {
        http_response_code(403);
        echo '<!doctype html>
        <html>
        <head><title>Access Denied</title></head>
        <body>
          <h2>403 — Access Denied</h2>
          <p>You do not have permission to view this page.</p>
          <a href="../index.php">← Go Home</a>
        </body>
        </html>';
        exit;
    }
}

// Helper functions
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isBuyer(): bool {
    return ($_SESSION['role'] ?? '') === 'buyer';
}

function isSeller(): bool {
    return ($_SESSION['role'] ?? '') === 'seller';
}

function isAdmin(): bool {
    return ($_SESSION['role'] ?? '') === 'admin';
}

function userName(): string {
    return htmlspecialchars($_SESSION['firstname'] ?? '');
}