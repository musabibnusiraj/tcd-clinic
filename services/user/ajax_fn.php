<?php
require_once '../../config.php';
require_once '../../helpers/AppManager.php';
require_once '../../models/User.php';
// require_once '../models/Doctor.php';

// Define target directory
$target_dir = "../assets/uploads/";

//create user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_user') {

    try {
        $username = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $permission = $_POST['permission'] ?? '';

        // $doctor_name = $_POST['doctor_name'] ?? null;
        // $about_doctor = $_POST['about_doctor'] ?? null;

        // // Get file information
        // $image = $_FILES["image"] ?? null;
        // $imageFileName = null;

        // // Check if file is uploaded
        // if (isset($image) && !empty($image)) {
        //     // Check if there are errors
        //     if ($image["error"] > 0) {
        //         echo json_encode(['success' => false, 'message' => "Error uploading file: " . $image["error"]]);
        //         exit;
        //     } else {
        //         // Check if file is an image
        //         if (getimagesize($image["tmp_name"]) !== false) {
        //             // Check file size (optional)
        //             if ($image["size"] < 500000) { // 500kb limit
        //                 // Generate unique filename
        //                 $new_filename = uniqid() . "." . pathinfo($image["name"])["extension"];

        //                 // Move uploaded file to target directory
        //                 if (move_uploaded_file($image["tmp_name"], $target_dir . $new_filename)) {
        //                     $imageFileName = $new_filename;
        //                 } else {
        //                     echo json_encode(['success' => false, 'message' => "Error moving uploaded file."]);
        //                     exit;
        //                 }
        //             } else {
        //                 echo json_encode(['success' => false, 'message' => "File size is too large."]);
        //                 exit;
        //             }
        //         } else {
        //             echo json_encode(['success' => false, 'message' => "Uploaded file is not an image."]);
        //             exit;
        //         }
        //     }
        // }

        $userModel = new User();
        $created =  $userModel->createUser($username, $password, $permission, $email);

        if ($created) {

            // if ($permission == 'doctor') {
            //     $user_id = $userModel->getLastInsertedUserId();

            //     $doctorModel = new Doctor();
            //     $doctorCreated =  $doctorModel->createDoctor($doctor_name,  $about_doctor, $user_id, $imageFileName);
            // }

            echo json_encode(['success' => true, 'message' => "User created successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create user. May be user already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}


//Delete by user id
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id']) && isset($_POST['action']) && $_POST['action'] == 'delete_user') {
    try {
        $user_id = $_POST['user_id'];
        $permission = $_POST['permission'];

        $userModel = new User();
        // $doctorModel = new Doctor();

        // // Check permission and delete doctor if necessary
        // if ($permission == 'doctor') {
        //     $doctorDeleted = $doctorModel->deleteDoctorByUserId($user_id);
        //     if ($doctorDeleted === false) {
        //         echo json_encode(['success' => false, 'message' => 'Doctor has appointments and cannot be deleted.']);
        //         exit;
        //     }
        // }

        // Proceed to delete the user if doctor deletion was successful or not needed
        $userDeleted = $userModel->deleteUser($user_id);

        if ($userDeleted) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// //Get user by id
// if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id']) && isset($_GET['action']) &&  $_GET['action'] == 'get_user') {

//     try {
//         $user_id = $_GET['user_id'];
//         $userModel = new User();
//         $user = $userModel->getUserWithDoctorById($user_id);
//         if ($user) {
//             echo json_encode(['success' => true, 'message' => "User created successfully!", 'data' => $user]);
//         } else {
//             echo json_encode(['success' => false, 'message' => 'Failed to create user. May be user already exist!']);
//         }
//     } catch (PDOException $e) {
//         // Handle database connection errors
//         echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
//     }
//     exit;
// }


// //update user
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_user') {

//     try {
//         $username = $_POST['user_name'] ?? '';
//         $email = $_POST['email'] ?? '';
//         $password = $_POST['password'] ?? "";
//         $cpassword = $_POST['confirm_password'] ?? "";
//         $permission = $_POST['permission'] ?? 'doctor';
//         $is_active = $_POST['is_active'] == 1 ? 1 : 0;
//         $id = $_POST['id'];

//         // Validate inputs
//         if (empty($username) || empty($email) || empty($password) || empty($cpassword)) {
//             echo json_encode(['success' => false, 'message' => 'Required fields are missing!']);
//             exit;
//         }

//         // Validate inputs
//         if (($password) != $cpassword) {
//             echo json_encode(['success' => false, 'message' => 'Passwords do not match..!']);
//             exit;
//         }

//         // Validate email format
//         if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//             echo json_encode(['success' => false, 'message' => 'Invalid email address']);
//             exit;
//         }

//         $userModel = new User();
//         $updated =  $userModel->updateUser($id, $username, $password, $permission, $email, $is_active);
//         if ($updated) {
//             echo json_encode(['success' => true, 'message' => "User updated successfully!"]);
//         } else {
//             echo json_encode(['success' => false, 'message' => 'Failed to update user. May be user already exist!']);
//         }
//     } catch (PDOException $e) {
//         // Handle database connection errors
//         echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
//     }
//     exit;
// }

dd('Access denied..!');
