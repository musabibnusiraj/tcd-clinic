<?php

/**
 * Entry point of the admin area.
 * Sends logged-in users to the dashboard and everyone else to the login page.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers/Auth.php';

if (Auth::isLoggedIn()) {
    header('Location: ' . url('views/admin/dashboard.php'));
} else {
    header('Location: ' . url('views/auth/login.php'));
}
exit;
