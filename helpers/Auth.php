<?php

/**
 * Project Name: Tooth Care - Channeling Appointments
 * Author: Musab Ibn Siraj
 */

require_once __DIR__ . '/AppManager.php';

/**
 * Auth - answers "who is logged in?" and protects pages / AJAX endpoints.
 *
 * Use at the top of a page:
 *   Auth::requireLogin();      // any logged-in user
 *   Auth::requireOperator();   // only operators (admins)
 */
class Auth
{
    /**
     * Returns the logged-in user's id (null when nobody is logged in).
     */
    public static function userId()
    {
        return AppManager::getSM()->getAttribute('userId');
    }

    /**
     * Returns the logged-in user's permission: 'operator' or 'doctor'.
     */
    public static function permission()
    {
        return AppManager::getSM()->getAttribute('permission');
    }

    /**
     * True when someone is logged in.
     */
    public static function isLoggedIn()
    {
        return !empty(self::userId());
    }

    /**
     * True when the logged-in user is an operator.
     */
    public static function isOperator()
    {
        return self::permission() === PERMISSION_OPERATOR;
    }

    /**
     * Protects a PAGE: sends visitors to the login page when not logged in.
     */
    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . url('views/auth/login.php'));
            exit; // Stop here, otherwise the rest of the page would still run
        }
    }

    /**
     * Protects an operator-only PAGE.
     */
    public static function requireOperator()
    {
        self::requireLogin();

        if (!self::isOperator()) {
            header('Location: ' . url('views/admin/dashboard.php'));
            exit;
        }
    }

    /**
     * Protects an AJAX endpoint: answers with a JSON error (not a redirect)
     * unless an operator is logged in.
     */
    public static function requireOperatorForAjax()
    {
        require_once __DIR__ . '/JsonResponse.php';

        if (!self::isOperator()) {
            JsonResponse::error('Access denied. Please log in as an operator.');
        }
    }
}
