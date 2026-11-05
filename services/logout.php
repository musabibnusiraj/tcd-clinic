<?php

/**
 * Logs the user out and sends them back to the start page.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers/AppManager.php';

AppManager::getSM()->destroy();

header('Location: ' . url('app.php'));
exit;
