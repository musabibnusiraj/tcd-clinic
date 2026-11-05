<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * Doctor - the doctor profile of a user (table: doctors).
 * Every doctor belongs to one user account (doctors.user_id).
 */
class Doctor extends BaseModel
{
    public $name;
    public $about;
    public $photo;
    public $is_active;
    public $user_id;

    protected function getTableName()
    {
        return 'doctors';
    }

    protected function addNewRec()
    {
        return $this->pm->insert(
            'INSERT INTO doctors (name, about, photo, is_active, user_id)
             VALUES (:name, :about, :photo, :is_active, :user_id)',
            [
                ':name'      => $this->name,
                ':about'     => $this->about,
                ':photo'     => $this->photo,
                ':is_active' => $this->is_active,
                ':user_id'   => $this->user_id,
            ]
        );
    }

    protected function updateRec()
    {
        return $this->pm->execute(
            'UPDATE doctors
             SET name = :name, about = :about, photo = :photo,
                 is_active = :is_active, user_id = :user_id
             WHERE id = :id',
            [
                ':name'      => $this->name,
                ':about'     => $this->about,
                ':photo'     => $this->photo,
                ':is_active' => $this->is_active,
                ':user_id'   => $this->user_id,
                ':id'        => $this->id,
            ]
        );
    }

    /**
     * Creates the doctor profile for a user.
     *
     * @return int The new doctor's id
     */
    public function createDoctor($name, $about, $userId, $photo = null, $isActive = 1)
    {
        $doctor = new Doctor();
        $doctor->name = $name;
        $doctor->about = $about;
        $doctor->user_id = $userId;
        $doctor->photo = $photo;
        $doctor->is_active = $isActive;

        return $doctor->save();
    }

    /**
     * Updates the name and "about" text of a doctor.
     * The photo, active flag and user are kept as they are.
     *
     * @return bool False when the doctor does not exist
     */
    public function updateDetails($doctorId, $name, $about)
    {
        $existing = $this->getById($doctorId);
        if (!$existing) {
            return false;
        }

        // Load the current row first so the fields we do not change stay the same
        $doctor = new Doctor();
        $doctor->fill($existing, ['id', 'name', 'about', 'photo', 'is_active', 'user_id']);

        $doctor->name = $name;
        $doctor->about = $about;
        $doctor->save();

        return true;
    }

    /**
     * Deletes the doctor profile that belongs to a user.
     *
     * @return bool True when there is nothing left to delete,
     *              false when the doctor has appointments (cannot be deleted)
     */
    public function deleteDoctorByUserId($userId)
    {
        $doctor = $this->getDoctorByUserId($userId);

        // The user has no doctor profile: nothing to delete
        if (!$doctor) {
            return true;
        }

        // Keep the appointment history: do not delete a doctor who has appointments
        if ($this->hasAppointments($doctor['id'])) {
            return false;
        }

        $this->deleteById($doctor['id']);
        return true;
    }

    /**
     * True when the doctor has at least one appointment.
     */
    public function hasAppointments($doctorId)
    {
        $row = $this->pm->fetchOne(
            'SELECT COUNT(*) AS appointment_count FROM appointments WHERE doctor_id = :doctor_id',
            [':doctor_id' => $doctorId]
        );

        return $row['appointment_count'] > 0;
    }

    /**
     * Returns the doctor profile of a user (false when the user is not a doctor).
     */
    public function getDoctorByUserId($userId)
    {
        return $this->pm->fetchOne(
            'SELECT * FROM doctors WHERE user_id = :user_id',
            [':user_id' => $userId]
        );
    }

    /**
     * Returns the active doctor with the smallest id (shown on the public home page).
     */
    public function getFirstActiveDoctor()
    {
        return $this->pm->fetchOne('SELECT * FROM doctors WHERE is_active = 1 ORDER BY id ASC LIMIT 1');
    }
}
