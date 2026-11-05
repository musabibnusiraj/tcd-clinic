<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Treatment.php';

/**
 * AppointmentController - AJAX actions for booking appointments
 * and managing their payments.
 */
class AppointmentController extends BaseController
{
    /** Request fields used when booking a new appointment */
    const BOOKING_FIELDS = [
        'appointment_no', 'doctor_id', 'patient_name', 'address', 'telephone', 'email',
        'nic', 'treatment_id', 'time_slot_from', 'time_slot_to', 'appointment_date',
    ];

    /** Patient details that can be edited after booking */
    const PATIENT_FIELDS = ['patient_name', 'address', 'telephone', 'email', 'nic'];

    protected function routes()
    {
        return [
            'POST' => [
                'book_appointment'   => 'book',
                'payment-save'       => 'savePayment',
                'appointment-update' => 'update',
            ],
        ];
    }

    /**
     * Books an appointment and creates its payment record.
     */
    protected function book()
    {
        $this->requireFields(
            ['doctor_id', 'treatment_id', 'patient_name', 'appointment_date', 'time_slot_from', 'time_slot_to'],
            'Appointment booking has an error! Required fields are missing.'
        );

        // The payment fees come from the chosen treatment
        $treatment = (new Treatment())->getById($this->input('treatment_id'));
        if (!$treatment) {
            JsonResponse::error('Appointment booking has an error! Treatment not found.');
        }

        // 1. Save the appointment
        $appointment = new Appointment();
        $appointment->fill($this->only(self::BOOKING_FIELDS), self::BOOKING_FIELDS);
        $appointmentId = $appointment->save();

        // 2. Create the payment: registration fee is paid at booking, treatment fee later
        $payment = new Payment();
        $payment->appointment_id = $appointmentId;
        $payment->registration_fee = $treatment['registration_fee'] ?? 0;
        $payment->registration_fee_paid = 1;
        $payment->treatment_fee = $treatment['treatment_fee'] ?? 0;
        $payment->quantity = 1;
        $payment->treatment_fee_paid = 0;
        $payment->save();

        JsonResponse::success('Appointment booked successfully');
    }

    /**
     * Updates the quantity and the "treatment fee paid" flag of a payment.
     */
    protected function savePayment()
    {
        $paymentId = $this->input('payment_id');

        if (!(new Payment())->getById($paymentId)) {
            JsonResponse::error('Payment has an error! Payment not found.');
        }

        $payment = new Payment();
        $payment->id = $paymentId;
        $payment->treatment_fee_paid = $this->input('treatment_fee_paid') ? 1 : 0;
        $payment->quantity = max(1, (int) $this->input('quantity', 1));
        $payment->save();

        JsonResponse::success('Payment updated successfully');
    }

    /**
     * Updates the patient's contact details of an appointment.
     */
    protected function update()
    {
        $appointmentId = $this->input('appointment_id');

        if (!(new Appointment())->getById($appointmentId)) {
            JsonResponse::error('Appointment has an error! Appointment not found.');
        }

        $appointment = new Appointment();
        $appointment->fill($this->only(self::PATIENT_FIELDS), self::PATIENT_FIELDS);
        $appointment->id = $appointmentId;
        $appointment->save();

        JsonResponse::success('Appointment updated successfully');
    }
}
