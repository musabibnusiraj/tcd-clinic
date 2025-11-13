<?php
// Include configuration and helper class files
include __DIR__ . '/../config.php';
include __DIR__ . '/../helpers/AppManager.php';

try {
    // Get instances of the persistence manager (PM) and session manager (SM)
    $pm = AppManager::getPM();
    $sm = AppManager::getSM();

    // Retrieve submitted credentials from the POST request (use null coalescing to avoid notices)
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;

    // Basic validation: ensure both fields are provided
    if (empty($email) || empty($password)) {
        // Set an error message in the session manager and redirect back
        $sm->setAttribute("error", 'Please fill all required fields!');
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../login.php'));
        exit;
    }

    // Prepare parameter for the query to fetch the user by email
    $param = array(':email' => $email);

    // Run a parameterized query to get the user row (true = single row)
    $user = $pm->run("SELECT * FROM users WHERE email = :email", $param, true);

    if ($user != null) {
        // Verify the provided password against the stored hash
        $correct = password_verify($password, $user['password']);
        if ($correct) {
            // On success, store user info in the session manager
            $sm->setAttribute("userId", $user['id']);
            $sm->setAttribute("username", $user['username']);
            $sm->setAttribute("permission", $user['permission']);

            if ($remember) {
                $cookie_time = time() + (30 * 24 * 60 * 60); // 30 days
                setcookie('remember_email', $email, $cookie_time, '/', '', false, true);
            } else {
                // Clear remember me cookies if unchecked
                if (isset($_COOKIE['remember_email'])) {
                    setcookie('remember_email', '', time() - 3600, '/', '', false, true);
                }
            }

            // Redirect to the main application page
            header('Location: ../app.php');
            exit;
        } else {
            // Wrong password: set generic error message
            $sm->setAttribute("error", 'Invalid email or password!');
        }
    } else {
        // No user found with that email: set generic error message
        $sm->setAttribute("error", 'Invalid email or password!');
    }

    // Redirect back to the referring page (login form)
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../login.php'));
    exit;
} catch (Exception $e) {
    $sm->setAttribute("error", 'An internal error occurred. Please try again later.');

    // Redirect back to the referring page or a fallback login page
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../login.php'));
    exit;
}
