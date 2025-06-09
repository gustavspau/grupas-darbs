<?php
require_once 'config.php';

// Function to login user
function login($email, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, email, password, first_name, last_name, role, status FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            
            // Set session security
            $_SESSION['last_activity'] = time();
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            
            return true;
        }
        return false;
    } catch(PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

// Function to check session security
function checkSessionSecurity() {
    if (!isset($_SESSION['last_activity']) || !isset($_SESSION['ip_address'])) {
        return false;
    }

    // Check if session has expired (30 minutes)
    if (time() - $_SESSION['last_activity'] > 1800) {
        logout();
        return false;
    }

    // Check if IP address has changed
    if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        logout();
        return false;
    }

    // Update last activity time
    $_SESSION['last_activity'] = time();
    return true;
}

// Function to logout user
function logout() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && checkSessionSecurity();
}

// Function to check if user is admin
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

// Function to check user role
function hasRole($required_role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $user_role = $_SESSION['role'];
    
    // Admin has access to everything
    if ($user_role === 'admin') {
        return true;
    }
    
    // Check specific role
    if ($user_role === $required_role) {
        return true;
    }
    
    return false;
}

// Function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Function to require specific role
function requireRole($required_role) {
    requireLogin();
    
    if (!hasRole($required_role)) {
        header("Location: access_denied.php");
        exit();
    }
}

// Function to get current user info
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['email'],
        'first_name' => $_SESSION['first_name'],
        'last_name' => $_SESSION['last_name'],
        'role' => $_SESSION['role']
    ];
}

// Function to hash password (for when adding users)
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
?> 