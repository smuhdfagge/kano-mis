<?php
class Home extends Controller {
    public function __construct() {
        
    }

    public function index() {
        // Redirect to login if not logged in
        if (!isset($_SESSION['user_id'])) {
            header('location: ' . URLROOT . '/users/login');
            exit();
        }
        
        // If logged in, redirect based on role
        switch($_SESSION['user_role']) {
            case 'administrator':
                header('location: ' . URLROOT . '/admin/dashboard');
                break;
            case 'editor':
                header('location: ' . URLROOT . '/editor/dashboard');
                break;
            case 'reviewer':
                header('location: ' . URLROOT . '/reviewer/dashboard');
                break;
            case 'viewer':
                header('location: ' . URLROOT . '/viewer/dashboard');
                break;
            default:
                header('location: ' . URLROOT . '/users/login');
        }
        exit();
    }
}