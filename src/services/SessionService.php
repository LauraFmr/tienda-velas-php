<?php

namespace services;


class SessionService
{
    private static $instance;
    private $expireAfterSeconds = 3600; // 1h


    private function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->refresh();
    }


    public static function getInstance(): SessionService
    {
        if (!self::$instance) self::$instance = new SessionService();
        return self::$instance;
    }


    private function refresh(): void
    {
        $now = time();
        if (!isset($_SESSION['last_activity'])) $_SESSION['last_activity'] = $now;
        if (($now - $_SESSION['last_activity']) > $this->expireAfterSeconds) $this->logout();
        $_SESSION['last_activity'] = $now;
    }


    public function login(array $userData): void
    {
        $_SESSION['user'] = $userData;
        $_SESSION['visits'] = ($_SESSION['visits'] ?? 0) + 1;
        $_SESSION['last_login'] = date('Y-m-d H:i:s');
    }


    public function logout(): void
    {
        $_SESSION = [];
        if (session_id()) session_destroy();
    }


    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['user']);
    }


    public function user()
    {
        return $_SESSION['user'] ?? null;
    }

    
    public function hasRole(string $role): bool
    {
        $u = $this->user();
        if (!$u) return false;
        return in_array($role, $u['roles'] ?? [], true);
    }
}
