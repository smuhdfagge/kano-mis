<?php
class Session {
    public static function init() {
        session_start();
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function hasRole($role) {
        if(isset($_SESSION['user_role'])) {
            return $_SESSION['user_role'] === $role;
        }
        return false;
    }

    public static function requireLogin() {
        if(!self::isLoggedIn()) {
            header('location: ' . URLROOT . '/users/login');
            exit;
        }
    }

    public static function requireRole($role) {
        self::requireLogin();
        if(!self::hasRole($role)) {
            header('location: ' . URLROOT . '/pages/unauthorized');
            exit;
        }
    }

    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function flash($name, $message = '') {
        if (!empty($message)) {
            $_SESSION['flash_' . $name] = $message;
        } else {
            if (isset($_SESSION['flash_' . $name])) {
                $msg = $_SESSION['flash_' . $name];
                unset($_SESSION['flash_' . $name]);
                return $msg;
            }
            return null;
        }
    }
}