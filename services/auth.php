<?php

/**
 * Login handler: checks the email and password sent by the login form.
 *
 * On success the user is saved in the session and sent to app.php.
 * On failure an error message is saved in the session and the user is
 * sent back to the login page.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers/AppManager.php';
require_once __DIR__ . '/../models/User.php';

$sm = AppManager::getSM();
$loginPage = url('views/auth/login.php');

/**
 * Saves an error message and returns to the login page.
 */
function failLogin(SessionManager $sm, $message, $loginPage)
{
    $sm->setAttribute('error', $message);
    header('Location: ' . $loginPage);
    exit;
}

try {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Both fields are required
    if ($email === '' || $password === '') {
        failLogin($sm, 'Please fill all required fields!', $loginPage);
    }

    $user = (new User())->getByEmail($email);

    // Use the same message for "no such user" and "wrong password"
    // so attackers cannot find out which emails are registered.
    if (!$user || !password_verify($password, $user['password'])) {
        failLogin($sm, 'Invalid email or password!', $loginPage);
    }

    // Inactive users are not allowed to log in
    if ((int) $user['is_active'] !== 1) {
        failLogin($sm, 'Your account is inactive. Please contact the administrator.', $loginPage);
    }

    // Login success: new session id (prevents session fixation) and remember the user
    $sm->regenerateId();
    $sm->setAttribute('userId', $user['id']);
    $sm->setAttribute('username', $user['username']);
    $sm->setAttribute('permission', $user['permission']);

    // "Remember me" only remembers the email on the login form (30 days)
    if ($remember) {
        setcookie('remember_email', $email, time() + 30 * 24 * 60 * 60, '/', '', false, true);
    } elseif (isset($_COOKIE['remember_email'])) {
        setcookie('remember_email', '', time() - 3600, '/', '', false, true);
    }

    header('Location: ' . url('app.php'));
    exit;
} catch (Exception $e) {
    error_log($e->getMessage());
    failLogin($sm, 'An internal error occurred. Please try again later.', $loginPage);
}
