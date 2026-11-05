<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * Appointment - a patient's booked time slot with a doctor (table: appointments).
 */
class Appointment extends BaseModel
{
    public $appointment_no;
    public $doctor_id;
    public $patient_name;
    public $address;
    public $telephone;
    public $email;
    public $nic;
    public $treatment_id;
    public $time_slot_from;
    public $time_slot_to;
    public $appointment_date;

    /** SQL used by every "list" query: appointment + treatment name + doctor name */
    const SELECT_WITH_DETAILS =
    'SELECT apt.*, tmt.name AS treatment_name, dr.name AS doctor_name
         FROM appointments AS apt
         INNER JOIN doctors AS dr ON apt.doctor_id = dr.id
         INNER JOIN treatments AS tmt ON tmt.id = apt.treatment_id';

    protected function getTableName()
    {
        return 'appointments';
    }

    protected function addNewRec()
    {
        return $this->pm->insert(
            'INSERT INTO appointments (appointment_no, doctor_id, patient_name, address, telephone,
                                       email, nic, treatment_id, time_slot_from, time_slot_to, appointment_date)
             VALUES (:appointment_no, :doctor_id, :patient_name, :address, :telephone,
                     :email, :nic, :treatment_id, :time_slot_from, :time_slot_to, :appointment_date)',
            [
                ':appointment_no'   => $this->appointment_no,
                ':doctor_id'        => $this->doctor_id,
                ':patient_name'     => $this->patient_name,
                ':address'          => $this->address,
                ':telephone'        => $this->telephone,
                ':email'            => $this->email,
                ':nic'              => $this->nic,
                ':treatment_id'     => $this->treatment_id,
                ':time_slot_from'   => $this->time_slot_from,
                ':time_slot_to'     => $this->time_slot_to,
                ':appointment_date' => $this->appointment_date,
            ]
        );
    }

    /**
     * Only the patient's contact details can be edited after booking.
     * (The doctor, treatment, date and time are fixed.)
     */
    protected function updateRec()
    {
        return $this->pm->execute(
            'UPDATE appointments
             SET patient_name = :patient_name,
                 address = :address,
                 telephone = :telephone,
                 email = :email,
                 nic = :nic
             WHERE id = :id',
            [
                ':patient_name' => $this->patient_name,
                ':address'      => $this->address,
                ':telephone'    => $this->telephone,
                ':email'        => $this->email,
                ':nic'          => $this->nic,
                ':id'           => $this->id,
            ]
        );
    }

    /**
     * Returns one appointment with its treatment name and doctor name.
     *
     * @return array|false The row, or false when it does not exist
     */
    public function getByIdWithDetails($id)
    {
        return $this->pm->fetchOne(
            self::SELECT_WITH_DETAILS . ' WHERE apt.id = :id',
            [':id' => $id]
        );
    }

    /**
     * Returns every appointment (newest first) - used by operators.
     */
    public function getAllWithDoctorAndTreatment()
    {
        return $this->pm->fetchAll(self::SELECT_WITH_DETAILS . ' ORDER BY apt.id DESC');
    }

    /**
     * Returns only the appointments of the doctor who owns $userId (newest first).
     */
    public function getAllWithDoctorAndTreatmentByUserId($userId)
    {
        return $this->pm->fetchAll(
            self::SELECT_WITH_DETAILS . ' WHERE dr.user_id = :user_id ORDER BY apt.id DESC',
            [':user_id' => $userId]
        );
    }
}
