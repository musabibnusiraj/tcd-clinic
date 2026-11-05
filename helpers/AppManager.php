<?php

/**
 * Project Name: Tooth Care - Channeling Appointments
 * Author: Musab Ibn Siraj
 */

require_once __DIR__ . '/PersistanceManager.php';
require_once __DIR__ . '/SessionManager.php';

/**
 * AppManager - gives the whole application ONE shared database manager
 * and ONE shared session manager (the "Singleton" idea).
 *
 * Without this, every model would open its own database connection.
 *
 * Usage:
 *   $pm = AppManager::getPM();   // database
 *   $sm = AppManager::getSM();   // session
 */
class AppManager
{
    /** @var PersistanceManager|null Shared database manager */
    private static $pm;

    /** @var SessionManager|null Shared session manager */
    private static $sm;

    /**
     * Returns the shared database manager (created on first use).
     */
    public static function getPM()
    {
        if (self::$pm === null) {
            self::$pm = new PersistanceManager();
        }
        return self::$pm;
    }

    /**
     * Returns the shared session manager (created on first use).
     */
    public static function getSM()
    {
        if (self::$sm === null) {
            self::$sm = new SessionManager();
        }
        return self::$sm;
    }
}
