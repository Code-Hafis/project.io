<?php
session_start();

// Dummy data for user credentials (replace with database check)
$valid_username = 'admin';
$valid_password = 'password'; // Ideally hashed password

// Set the max login attempts
$max_attempts = 3;

// Check if login attempts session variable is set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// If login attempts exceed the maximum limit
if ($_SESSION['login_attempts'] >= $max_attempts) {
    header("Location: index.php?error=Too many login attempts. Please try again later.");
    exit();
}

// reCAPTCHA secret key
$secret_key = 'your-secret-key';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $captcha = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$captcha");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        $_SESSION['login_attempts']++;
        header("Location: index.php?error=Invalid CAPTCHA. Please try again.");
        exit();
    }

    // Validate username and password
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['username'] = $username;
        $_SESSION['login_attempts'] = 0; // reset login attempts
        header("Location: welcome.php");
        exit();
    } else {
        $_SESSION['login_attempts']++;
        header("Location: index.php?error=Invalid username or password. Please try again.");
        exit();
    }
}
?>
