<?php
require_once '../../config.php';
require_once '../../helpers/AppManager.php';
// require_once '../../models/Doctor.php';
require_once '../../models/Appointment.php';
require_once '../../models/Payment.php';
require_once '../../models/Treatment.php';

//book_appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book_appointment') {

    try {
        $appointment = new Appointment();

        if (isset($_POST['id'])) {
            $appointment = $appointment->getById($_POST['id']);
        }

        $appointment->appointment_no = $_POST['appointment_no'] ?? '';
        $appointment->doctor_id = $_POST['doctor_id'] ?? null;
        $appointment->patient_name = $_POST['patient_name'] ?? null;
        $appointment->address = $_POST['address'] ?? null;
        $appointment->telephone = $_POST['telephone'] ?? null;
        $appointment->email = $_POST['email'] ?? null;
        $appointment->nic = $_POST['nic'] ?? null;
        $appointment->treatment_id = $_POST['treatment_id'] ?? null;
        $appointment->time_slot_from = $_POST['time_slot_from'] ?? null;
        $appointment->time_slot_to = $_POST['time_slot_to'] ?? null;
        $appointment->appointment_date = $_POST['appointment_date'] ?? null;

        $insertedId = $appointment->save();
        //
        $treatment = new Treatment();
        $appointmentTreatment = $treatment->getById($appointment->treatment_id);

        if (isset($insertedId) && isset($appointmentTreatment)) {

            $payment = new Payment();
            $payment->appointment_id = $insertedId;
            $payment->registration_fee = $appointmentTreatment['registration_fee'] ?? 0;
            $payment->registration_fee_paid = 1;
            $payment->treatment_fee = $appointmentTreatment['treatment_fee'] ?? 0;
            $payment->quantity = 1;
            $payment->treatment_fee_paid = 0;
            $payment->save();

            // Response to send back
            echo json_encode(['success' => true, 'message' => 'Appointment booked successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Appointment booking have an error!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

//payment-save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'payment-save') {

    try {

        $payment_id = $_POST['payment_id'] ?? null;
        $treatment_fee_paid = $_POST['treatment_fee_paid'] ? 1 : 0;
        $quantity = $_POST['quantity'] ?? 1;

        $payment = new Payment();
        $paymentData = $payment->getById($payment_id);

        if (isset($paymentData)) {
            $payment->id = $payment_id;
            $payment->treatment_fee_paid = $treatment_fee_paid ?? 0;
            $payment->quantity = $quantity ?? 0;
            $udpated = $payment->save();

            // Response to send back
            echo json_encode(['success' => true, 'message' => 'Payment udpated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Payment have an error!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// //update appointment
// if (
//     $_SERVER['REQUEST_METHOD'] === 'POST'
//     && isset($_POST['action'])
//     && $_POST['action'] === 'appointment-update'
// ) {
//     try {
//         $appointment_id = $_POST['appointment_id'] ?? null;
//         $patient_name = $_POST['patient_name'] ?? "";
//         $address = $_POST['address'] ?? "";
//         $telephone = $_POST['telephone'] ?? "";
//         $email = $_POST['email'] ?? "";
//         $nic = $_POST['nic'] ?? "";

//         $appointment = new Appointment();
//         $appointmentData = $appointment->getById($appointment_id);

//         if (!empty($appointmentData)) {
//             $appointment->id = $appointment_id;
//             $appointment->patient_name = $patient_name;
//             $appointment->address = $address;
//             $appointment->telephone = $telephone;
//             $appointment->address = $address;
//             $appointment->email = $email;
//             $appointment->nic = $nic;
//             $appointment->save();


//             // Response to send back
//             echo json_encode(['success' => true, 'message' => 'Appointment udpated successfully']);
//         } else {
//             echo json_encode(['success' => false, 'message' => 'Appointment have an error!']);
//         }
//     } catch (PDOException $e) {
//         // Handle database connection errors
//         echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
//     }
//     exit;
// }

dd('Access denied..!');
