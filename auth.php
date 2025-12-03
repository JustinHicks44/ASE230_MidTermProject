<?php
require_once __DIR__ . '/storage.php';

if (session_status() === PHP_SESSION_NONE) session_start();

function auth_init_default_admin(): void {
    $users = storage_read('users');
    if (count($users) === 0) {
        $admin = [
            'id' => uniqid(),
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => password_hash('Admin@123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'created_at' => time()
        ];
        storage_write('users', [$admin]);
    }
}

function auth_get_user_by_username(string $username): ?array {
    return storage_find('users', function($u) use ($username) {
        return isset($u['username']) && $u['username'] === $username;
    });
}

function auth_signup(string $username, string $email, string $password): array {
    // simple validation
    if (empty($username) || empty($email) || empty($password)) {
        return ['ok' => false, 'error' => 'All fields required'];
    }
    if (auth_get_user_by_username($username)) {
        return ['ok' => false, 'error' => 'Username already taken'];
    }
    $user = [
        'id' => uniqid(),
        'username' => $username,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'user',
        'created_at' => time()
    ];
    $users = storage_read('users');
    $users[] = $user;
    storage_write('users', $users);
    // auto-login after signup
    $_SESSION['user_id'] = $user['id'];
    return ['ok' => true, 'user' => $user];
}

function auth_signin(string $username, string $password): array {
    $u = auth_get_user_by_username($username);
    if (!$u) return ['ok' => false, 'error' => 'Invalid credentials'];
    if (!password_verify($password, $u['password'])) return ['ok' => false, 'error' => 'Invalid credentials'];
    $_SESSION['user_id'] = $u['id'];
    return ['ok' => true, 'user' => $u];
}

function auth_signout(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    unset($_SESSION['user_id']);
}

function auth_current_user(): ?array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user_id'])) return null;
    $id = $_SESSION['user_id'];
    return storage_find('users', function($u) use ($id) {
        return isset($u['id']) && $u['id'] === $id;
    });
}

function auth_require_role(array $roles): void {
    $user = auth_current_user();
    if (!$user || !in_array($user['role'] ?? 'user', $roles, true)) {
        header('Location: login.php');
        exit;
    }
}
