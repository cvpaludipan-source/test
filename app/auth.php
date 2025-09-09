<?php
declare(strict_types=1);

enum UserRole: string
{
    case ADMIN = 'admin';
    case STAFF = 'staff';
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_require(): void
{
    if (!auth_user()) {
        header('Location: /login');
        exit;
    }
}

function auth_require_role(UserRole $role): void
{
    auth_require();
    $user = auth_user();
    if (!$user || ($user['role'] ?? null) !== $role->value) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

function auth_login(PDO $pdo, string $username, string $password): bool
{
    $stmt = $pdo->prepare('SELECT id, username, role, password_hash, full_name FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'full_name' => $user['full_name'],
        ];
        return true;
    }
    return false;
}

function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

