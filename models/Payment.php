<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * Payment - the money part of one appointment (table: payments).
 * One payment row is created automatically when an appointment is booked.
 */
class Payment extends BaseModel
{
    public $appointment_id;
    public $registration_fee;
    public $registration_fee_paid;
    public $treatment_fee;
    public $quantity;
    public $treatment_fee_paid;

    protected function getTableName()
    {
        return 'payments';
    }

    protected function addNewRec()
    {
        return $this->pm->insert(
            'INSERT INTO payments (appointment_id, registration_fee, registration_fee_paid,
                                   treatment_fee, quantity, treatment_fee_paid)
             VALUES (:appointment_id, :registration_fee, :registration_fee_paid,
                     :treatment_fee, :quantity, :treatment_fee_paid)',
            [
                ':appointment_id'        => $this->appointment_id,
                ':registration_fee'      => $this->registration_fee,
                ':registration_fee_paid' => $this->registration_fee_paid,
                ':treatment_fee'         => $this->treatment_fee,
                ':quantity'              => $this->quantity,
                ':treatment_fee_paid'    => $this->treatment_fee_paid,
            ]
        );
    }

    /**
     * Only the quantity and the "treatment fee paid" flag can be changed later.
     */
    protected function updateRec()
    {
        return $this->pm->execute(
            'UPDATE payments
             SET quantity = :quantity, treatment_fee_paid = :treatment_fee_paid
             WHERE id = :id',
            [
                ':quantity'           => $this->quantity,
                ':treatment_fee_paid' => $this->treatment_fee_paid,
                ':id'                 => $this->id,
            ]
        );
    }

    /**
     * Returns every payment with its treatment name and appointment number.
     */
    public function getAllWithTreatmentAndAppointment()
    {
        return $this->pm->fetchAll(
            'SELECT pmt.*, tmt.name AS treatment_name, tmt.id AS treatment_id,
                    apt.appointment_no AS appointment_no
             FROM payments AS pmt
             INNER JOIN appointments AS apt ON apt.id = pmt.appointment_id
             INNER JOIN treatments AS tmt ON tmt.id = apt.treatment_id'
        );
    }
}
