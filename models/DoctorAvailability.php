<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * DoctorAvailability - a weekly time session when a doctor is available
 * (table: doctor_availability). Example: Monday 18:00 - 21:00.
 */
class DoctorAvailability extends BaseModel
{
    public $day;
    public $session_from;
    public $session_to;
    public $doctor_id;
    public $is_active;

    /** Day names exactly as they are stored in the `day` column */
    const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    protected function getTableName()
    {
        return 'doctor_availability';
    }

    /**
     * Returns every availability together with the doctor's name.
     */
    public function getAll()
    {
        return $this->pm->fetchAll(
            'SELECT da.*, dr.name AS doctor_name
             FROM doctor_availability AS da
             JOIN doctors AS dr ON da.doctor_id = dr.id'
        );
    }

    /**
     * Returns the active availabilities of one doctor.
     */
    public function getAllActiveByDoctorId($doctorId)
    {
        return $this->pm->fetchAll(
            'SELECT * FROM doctor_availability WHERE is_active = 1 AND doctor_id = :doctor_id',
            [':doctor_id' => $doctorId]
        );
    }

    /**
     * True when the doctor already has a session on this day that overlaps
     * the given time range (two sessions at the same time would create
     * duplicate appointment slots).
     *
     * @param string   $from      Session start, 'HH:MM:SS'
     * @param string   $to        Session end, 'HH:MM:SS'
     * @param int|null $excludeId Ignore this availability (use it when editing)
     */
    public function hasOverlappingSession($doctorId, $day, $from, $to, $excludeId = null)
    {
        $sql = 'SELECT id FROM doctor_availability
                WHERE doctor_id = :doctor_id AND day = :day
                  AND session_from < :to AND session_to > :from';
        $params = [':doctor_id' => $doctorId, ':day' => $day, ':from' => $from, ':to' => $to];

        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude_id';
            $params[':exclude_id'] = $excludeId;
        }

        return (bool) $this->pm->fetchOne($sql, $params);
    }

    protected function addNewRec()
    {
        return $this->pm->insert(
            'INSERT INTO doctor_availability (day, session_from, session_to, doctor_id, is_active)
             VALUES (:day, :session_from, :session_to, :doctor_id, :is_active)',
            [
                ':day'          => $this->day,
                ':session_from' => $this->session_from,
                ':session_to'   => $this->session_to,
                ':doctor_id'    => $this->doctor_id,
                ':is_active'    => $this->is_active,
            ]
        );
    }

    protected function updateRec()
    {
        return $this->pm->execute(
            'UPDATE doctor_availability
             SET day = :day,
                 session_from = :session_from,
                 session_to = :session_to,
                 doctor_id = :doctor_id,
                 is_active = :is_active
             WHERE id = :id',
            [
                ':day'          => $this->day,
                ':session_from' => $this->session_from,
                ':session_to'   => $this->session_to,
                ':doctor_id'    => $this->doctor_id,
                ':is_active'    => $this->is_active,
                ':id'           => $this->id,
            ]
        );
    }
}
