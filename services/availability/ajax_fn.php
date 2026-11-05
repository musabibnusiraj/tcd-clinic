<?php

/**
 * AJAX endpoint for the Doctor Availabilities page.
 * All the work is done by DoctorAvailabilityController
 * (see controllers/DoctorAvailabilityController.php).
 */
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/DoctorAvailabilityController.php';

(new DoctorAvailabilityController())->handle();
