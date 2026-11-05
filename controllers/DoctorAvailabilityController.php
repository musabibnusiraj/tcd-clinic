<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/DoctorAvailability.php';
require_once __DIR__ . '/../models/Doctor.php';

/**
 * DoctorAvailabilityController - AJAX actions for the Doctor Availabilities page.
 *
 * An availability is a weekly session of a doctor, e.g. "monday 18:00 - 21:00".
 * The appointment calendar turns each session into bookable time slots.
 */
class DoctorAvailabilityController extends BaseController
{
    /** Request fields that belong to an availability */
    const FIELDS = ['day', 'session_from', 'session_to', 'doctor_id', 'is_active'];

    protected function routes()
    {
        return [
            'POST' => [
                'create_availability' => 'create',
                'update_availability' => 'update',
                'delete_availability' => 'delete',
            ],
            'GET' => [
                'get_availability' => 'get',
            ],
        ];
    }

    /**
     * Adds a new session for a doctor.
     */
    protected function create()
    {
        $availability = $this->buildFromRequest();
        $availability->save();

        JsonResponse::success('Availability created successfully!');
    }

    /**
     * Updates an existing session.
     */
    protected function update()
    {
        $id = $this->input('id');

        if (!(new DoctorAvailability())->getById($id)) {
            JsonResponse::error('Availability not found.');
        }

        $availability = $this->buildFromRequest($id);
        $availability->id = $id;
        $availability->save();

        JsonResponse::success('Availability updated successfully!');
    }

    /**
     * Returns one session (used to fill the edit form).
     */
    protected function get()
    {
        $availability = (new DoctorAvailability())->getById($this->input('id'));

        if (!$availability) {
            JsonResponse::error('Availability not found.');
        }

        JsonResponse::success('Availability data retrieved successfully!', $availability);
    }

    /**
     * Deletes a session. Appointments that were already booked are not affected.
     */
    protected function delete()
    {
        $deleted = (new DoctorAvailability())->deleteById($this->input('id'));

        if (!$deleted) {
            JsonResponse::error('Availability not found.');
        }

        JsonResponse::success('Availability deleted successfully!');
    }

    // -----------------------------------------------------------------
    // Helpers (shared by create() and update())
    // -----------------------------------------------------------------

    /**
     * Validates the request and returns a DoctorAvailability ready to save.
     *
     * @param int|null $excludeId The availability being edited (null when creating)
     * @throws InvalidArgumentException With the message to show to the user
     */
    private function buildFromRequest($excludeId = null)
    {
        $this->requireFields(['day', 'session_from', 'session_to', 'doctor_id']);

        $day = strtolower($this->input('day'));
        if (!in_array($day, DoctorAvailability::DAYS, true)) {
            throw new InvalidArgumentException('Invalid day.');
        }

        if (!(new Doctor())->getById($this->input('doctor_id'))) {
            throw new InvalidArgumentException('Doctor not found.');
        }

        $from = $this->normalizeTime($this->input('session_from'));
        $to = $this->normalizeTime($this->input('session_to'));
        if ($from >= $to) {
            throw new InvalidArgumentException('The end time must be after the start time.');
        }

        $availability = new DoctorAvailability();
        if ($availability->hasOverlappingSession($this->input('doctor_id'), $day, $from, $to, $excludeId)) {
            throw new InvalidArgumentException('This doctor already has a session that overlaps this time.');
        }

        $availability->fill($this->only(self::FIELDS), self::FIELDS);
        $availability->day = $day;
        $availability->session_from = $from;
        $availability->session_to = $to;
        $availability->is_active = ($this->input('is_active') == 1) ? 1 : 0;

        return $availability;
    }

    /**
     * Converts '18:00' or '18:00:00' to 'HH:MM:SS' (the format stored in the database).
     *
     * @throws InvalidArgumentException When the text is not a valid time
     */
    private function normalizeTime($time)
    {
        foreach (['H:i:s', 'H:i'] as $format) {
            $date = DateTime::createFromFormat($format, $time);
            if ($date && $date->format($format) === $time) {
                return $date->format('H:i:s');
            }
        }
        throw new InvalidArgumentException('Invalid time.');
    }
}
