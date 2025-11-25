
<?php
require_once '../../config.php';
require_once '../../helpers/AppManager.php';
require_once '../../models/Treatment.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'create_treatment') {

        $name = $_POST['name'];
        $description = $_POST['description'];
        $registration_fee = $_POST['registration_fee'];
        $treatment_fee = $_POST['treatment_fee'];
        $is_active = $_POST['is_active'];

        $treatment = new Treatment();
        $treatment->name = $name;
        $treatment->description = $description;
        $treatment->registration_fee = $registration_fee;
        $treatment->treatment_fee = $treatment_fee;
        $treatment->is_active = $is_active;
        $treatment->save();

        if ($treatment) {
            echo json_encode(['success' => true, 'message' => 'Treatment create successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create treatment.']);
        }
        exit;
    }

    if (isset($_POST['id']) &&  $_POST['action'] === 'update_treatment') {

        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $registration_fee = $_POST['registration_fee'];
        $treatment_fee = $_POST['treatment_fee'];
        $is_active = $_POST['is_active'];

        $treatment = new Treatment();
        $treatment->id = $id;
        $treatment->name = $name;
        $treatment->description = $description;
        $treatment->registration_fee = $registration_fee;
        $treatment->treatment_fee = $treatment_fee;
        $treatment->is_active = $is_active;
        $treatment->save();

        if ($treatment) {
            echo json_encode(['success' => true, 'message' => 'Treatment update successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update treatment.']);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {

    //Get treatmnet by id
    if (isset($_GET['id']) && $_GET['action'] == 'get_treatment') {

        try {
            $id = $_GET['id'];
            $treatment = new Treatment();
            $treatment = $treatment->getById($id);
            if ($treatment) {
                echo json_encode(['success' => true, 'message' => "Treatment created successfully!", 'data' => $treatment]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create user. May be user already exist!']);
            }
        } catch (PDOException $e) {
            // Handle database connection errors
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    //Delete by treatment id
    if (isset($_GET['id']) && $_GET['action'] == 'delete_treatment') {
        try {

            $id = $_GET['id'];
            $treatmentModel = new Treatment();
            $treatmentDeleted = $treatmentModel->deleteTreatment($id);

            if ($treatmentDeleted) {
                echo json_encode(['success' => true, 'message' => 'Treatment deleted successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete treatment.']);
            }
        } catch (PDOException $e) {
            // Handle database connection errors
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
}



dd('Access denied..!');
