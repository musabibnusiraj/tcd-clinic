<?php

/**
 * AJAX endpoint for the Users page.
 * All the work is done by UserController (see controllers/UserController.php).
 */
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/UserController.php';

(new UserController())->handle();
