<?php
class Users extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function login() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];

            // Validate email
            if(empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }

            // Validate password
            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Check for user/email
            if($this->userModel->findUserByEmail($data['email'])) {
                // User found
            } else {
                $data['email_err'] = 'No user found';
            }

            // Make sure errors are empty
            if(empty($data['email_err']) && empty($data['password_err'])) {
                // Validated
                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if($loggedInUser) {
                    // Create Session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password incorrect';
                    $this->view('users/login', $data);
                }
            } else {
                // Load view with errors
                $this->view('users/login', $data);
            }
        } else {
            // Init data
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => ''
            ];

            // Load view
            $this->view('users/login', $data);
        }
    }

    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_role'] = $user->role;
        
        // Redirect based on role
        switch($user->role) {
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
                header('location: ' . URLROOT);
        }
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
        session_destroy();
        header('location: ' . URLROOT . '/users/login');
    }
}