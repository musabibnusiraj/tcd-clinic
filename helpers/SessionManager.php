<?php

/**
 * Project Name: Tooth Care - Channeling Appointments
 * Author: Musab Ibn Siraj
 */

/**
 * SessionManager - a small wrapper around PHP's $_SESSION.
 *
 * Sessions remember who is logged in between page requests.
 */
class SessionManager
{
    /**
     * Starts the session if it is not started yet.
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // session_start() cannot work after the page has already printed something
            if (!headers_sent()) {
                session_start();
            } else {
                error_log('SessionManager: headers already sent; session_start() skipped.');
            }
        }
    }

    /**
     * Saves a value in the session.
     */
    public function setAttribute($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Reads a value from the session (null when it does not exist).
     */
    public function getAttribute($key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Removes a value from the session.
     */
    public function removeAttribute($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * Checks whether a value exists in the session.
     */
    public function isKeySet($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Creates a new session id (call this right after login to stop session fixation).
     */
    public function regenerateId()
    {
        session_regenerate_id(true);
    }

    /**
     * Logs the user out by destroying all session data.
     */
    public function destroy()
    {
        session_unset();
        session_destroy();
    }
}
