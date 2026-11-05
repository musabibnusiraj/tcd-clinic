<?php

/**
 * AJAX endpoint for booking appointments and saving payments.
 * All the work is done by AppointmentController (see controllers/AppointmentController.php).
 */
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/AppointmentController.php';

(new AppointmentController())->handle();
