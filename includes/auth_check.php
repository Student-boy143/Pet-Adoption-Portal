<?php
// includes/auth_check.php
// Usage:
//   require_once 'includes/auth_check.php';           // any logged-in user
//   require_once 'includes/auth_check.php'; requireRole('seller');
//   require_once 'includes/auth_check.php'; requireRole('admin');

if (session_status() === PHP_SESSION_NONE) session_start();

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
        die('<h2>Access Denied</h2><p>You do not have permission to view this page.</p><a href="/index.php">Go Home</a>');
    }
}

function isLoggedIn(): bool  { return isset($_SESSION['user_id']); }
function isBuyer(): bool     { return ($_SESSION['role'] ?? '') === 'buyer';  }
function isSeller(): bool    { return ($_SESSION['role'] ?? '') === 'seller'; }
function isAdmin(): bool     { return ($_SESSION['role'] ?? '') === 'admin';  }
function userName(): string  { return htmlspecialchars($_SESSION['firstname'] ?? ''); }