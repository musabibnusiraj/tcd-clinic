<?php

/**
 * Application configuration and small global helper functions.
 *
 * This file is included by every page, so keep it light:
 * only constants and tiny, reusable functions live here.
 *
 * Project Name: Tooth Care - Channeling Appointments
 * Author: Musab Ibn Siraj
 */

// ---------------------------------------------------------------------
// Database settings (change these for your own server)
// ---------------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'clinic');

// ---------------------------------------------------------------------
// Application settings
// ---------------------------------------------------------------------
define('BASE_PATH', __DIR__);          // Absolute folder path of the project (for PHP includes)
define('APP_FOLDER', '/');   // Folder name under the web root (used to build URLs)
define('SLOT_DURATION', 'PT60M');      // Length of one appointment slot (ISO 8601: 60 minutes)

// Permission names stored in the `users.permission` column
define('PERMISSION_OPERATOR', 'operator');
define('PERMISSION_DOCTOR', 'doctor');

// Set the timezone used by all date/time functions
date_default_timezone_set('Asia/Colombo');

// ---------------------------------------------------------------------
// URL helpers
// ---------------------------------------------------------------------

/**
 * Returns "https://" or "http://" depending on how the page was requested.
 */
function protocol()
{
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    return $isHttps ? 'https://' : 'http://';
}

/**
 * Returns the root URL of the application, e.g. "https://tcd-clinic.test".
 */
function current_domain()
{
    return protocol() . $_SERVER['HTTP_HOST'] . APP_FOLDER;
}

// Root URL, calculated once and reused by url() and asset()
define('CURRENT_DOMAIN', current_domain());

/**
 * Returns the full URL of the page that is currently open.
 */
function currentUrl()
{
    return current_domain() . $_SERVER['REQUEST_URI'];
}

/**
 * Builds a full URL for a path inside the project.
 * Example: url('views/admin/users.php')
 */
function url($path)
{
    return rtrim(CURRENT_DOMAIN, '/ ') . '/' . trim($path, '/ ');
}

/**
 * Builds a full URL for a file inside the project (css, js, images ...).
 * Example: asset('assets/img/avatars/1.png')
 */
function asset($path)
{
    return url($path);
}

// ---------------------------------------------------------------------
// Output helpers
// ---------------------------------------------------------------------

/**
 * Escapes text so it is safe to print inside HTML (prevents XSS attacks).
 * Always use this when printing data that came from the database or a user.
 * Example: <?= e($doctor['name']) ?>
 */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Generates a random hexadecimal string (used for appointment numbers).
 * Example: generateRandomString(4) => "a3f9"
 */
function generateRandomString($length = 4)
{
    return bin2hex(random_bytes($length / 2));
}

// ---------------------------------------------------------------------
// Debug helpers (development only)
// ---------------------------------------------------------------------

/**
 * Prints a value in a readable way (print).
 */
function pr($data, $comment = '')
{
    print('<pre>');
    print($comment);
    print('<br>');
    print_r($data);
    print('</pre>');
}

/**
 * Prints a value and stops the script (dump and die).
 */
function dd($data, $comment = '')
{
    pr($data, $comment);
    die;
}
