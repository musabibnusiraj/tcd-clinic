<?php

/**
 * AJAX endpoint for the Treatments page.
 * All the work is done by TreatmentController (see controllers/TreatmentController.php).
 */
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/TreatmentController.php';

(new TreatmentController())->handle();
