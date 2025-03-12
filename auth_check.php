<?php
session_start();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Function to check if user is instructor
function isInstructor() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'instructor';
}

// Function to require admin access
function requireAdmin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    
    if (isInstructor()) {
        header("Location: restrict.php");
        exit();
    }
    
    if (!isAdmin()) {
        header("Location: login.php");
        exit();
    }
}

// Function to require instructor or admin access
function requireInstructorOrAdmin() {
    if (!isLoggedIn() || !(isInstructor() || isAdmin())) {
        header("Location: login.php");
        exit();
    }
}
?> 