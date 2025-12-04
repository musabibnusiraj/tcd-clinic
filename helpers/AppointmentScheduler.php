<?php
require_once __DIR__ . '/../models/DoctorAvailability.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Treatment.php';

/**
 * AppointmentScheduler - Manages doctor appointment scheduling and availability
 * 
 * This class handles:
 * - Displaying available appointment slots for a specific doctor
 * - Checking slot availability against existing appointments
 * - Generating weekly schedule view
 * - Managing time slot conflicts
 */
class AppointmentScheduler
{
    /** @var int Doctor's unique identifier */
    private $doctorId;

    /** @var string Week selection in format 'YYYY-WW' */
    private $weekInputValue;

    /** @var DateTime First day of the selected week */
    private $firstDayOfWeek;

    /** @var array Days of the week (e.g., ['Monday', 'Tuesday', ...]) */
    public $days;

    /** @var array Doctor's available time slots from database */
    private $availableSlots;

    /** @var DateTime Current date and time */
    private $today;

    /** @var array All existing appointments for this doctor */
    private $existingAppointments;

    /** @var DateInterval Duration of each appointment slot (e.g., 30 minutes) */
    private $slotDuration;

    /**
     * Initialize the appointment scheduler
     * 
     * Now handles all data fetching internally for better encapsulation.
     * The view layer only needs to provide the doctor ID and week selection.
     * 
     * @param int    $doctorId         Doctor's ID
     * @param string $weekInputValue   Week in 'YYYY-WW' format (e.g., '2025-W01')
     */
    public function __construct($doctorId, $weekInputValue)
    {
        $this->doctorId = $doctorId;
        $this->weekInputValue = $weekInputValue;

        // Initialize current date for validation
        $this->today = new DateTime();

        // Get days of the week
        $this->days = $this->getDays();

        // Convert slot duration from constant to DateInterval
        $this->slotDuration = new DateInterval(SLOT_DURATION);

        // Calculate the first day of the selected week
        $this->calculateWeekStart();

        // Fetch all necessary data from database
        $this->loadData();
    }

    /**
     * Load all required data from the database
     * 
     * This method encapsulates all data fetching logic, keeping it
     * separate from the view layer and making the class more self-contained.
     */
    private function loadData()
    {
        // Fetch doctor's availability slots
        $doctorAvailabilityModel = new DoctorAvailability();
        $this->availableSlots = $doctorAvailabilityModel->getAllActiveByDoctorId($this->doctorId);

        // Fetch existing appointments for this doctor
        $appointmentModel = new Appointment();
        $this->existingAppointments = $appointmentModel->getAllByColumnValue('doctor_id', $this->doctorId);
    }

    /**
     * Calculate the first day (Monday) of the selected week
     * 
     * Converts week input like '2025-W01' to actual date
     */
    private function calculateWeekStart()
    {
        // Parse year and week number from format 'YYYY-WW'
        list($year, $week) = sscanf($this->weekInputValue, "%d-W%d");

        // Create DateTime and set to Monday (day 1) of the ISO week
        $this->firstDayOfWeek = new DateTime();
        $this->firstDayOfWeek->setISODate($year, $week, 1);
    }

    /**
     * Display the complete appointment schedule
     * 
     * Main public method that renders the weekly schedule view
     */
    public function displaySchedule()
    {
        // Fetch doctor information
        $doctor = $this->getDoctorDetails();

        // Render header with doctor info and back button
        echo $this->renderDoctorHeader($doctor);

        // Render the main schedule content
        echo '<section class="content m-3">';
        echo '<div class="container-fluid">';

        if (empty($this->availableSlots)) {
            echo $this->renderNoAvailabilityMessage();
        } else {
            $this->renderWeeklySchedule();
        }

        echo '</div>';
        echo '</section>';
        echo '</div>';
    }

    /**
     * Get doctor details from database
     * 
     * @return array Doctor information
     */
    private function getDoctorDetails()
    {
        $doctorModel = new Doctor();
        return $doctorModel->getById($this->doctorId);
    }

    /**
     * Public getter for doctor details
     * 
     * Used by the view to populate the booking modal
     * 
     * @return array Doctor information
     */
    public function getDoctor()
    {
        return $this->getDoctorDetails();
    }

    /**
     * Get all active treatments
     * 
     * Used by the view to populate the treatment dropdown in the booking modal
     * 
     * @return array List of active treatments
     */
    public function getTreatments()
    {
        $treatmentModel = new Treatment();
        return $treatmentModel->getAllActive();
    }

    /**
     * Render the weekly schedule with all available slots
     */
    private function renderWeeklySchedule()
    {
        // Iterate through each day of the week (Monday to Sunday)
        foreach ($this->days as $dayIndex => $dayName) {
            $date = $this->calculateDate($dayIndex);
            $this->renderDaySchedule($dayName, $date);
        }
    }

    /**
     * Calculate date for a specific day in the week
     * 
     * @param int $dayIndex Day index (0 = Monday, 6 = Sunday)
     * @return DateTime Date object for the day
     */
    private function calculateDate($dayIndex)
    {
        $date = clone $this->firstDayOfWeek;
        $date->modify("+{$dayIndex} days");
        return $date;
    }

    /**
     * Render schedule for a specific day
     * 
     * @param string   $dayName Day of the week (e.g., 'Monday')
     * @param DateTime $date    Date object for this day
     */
    private function renderDaySchedule($dayName, $date)
    {
        // Check if doctor has availability on this day
        $daySlots = $this->getSlotsForDay($dayName);

        if (empty($daySlots)) {
            return; // No slots for this day, skip rendering
        }

        $dateFormatted = $date->format('l, F j, Y'); // e.g., 'Monday, January 1, 2025'
        $dateString = $date->format('Y-m-d'); // e.g., '2025-01-01'

        // Render day header
        echo '<div class="row my-5 border rounded border-secondary">';
        echo '<h3 class="mt-4 text-capitalize">' . $dateFormatted . '</h3>';

        // Render all time slots for this day
        foreach ($daySlots as $slot) {
            $this->renderTimeSlots($slot, $date, $dateString);
        }

        echo '</div>';
    }

    /**
     * Get availability slots for a specific day
     * 
     * @param string $dayName Day of the week
     * @return array Slots available on this day
     */
    private function getSlotsForDay($dayName)
    {
        $slots = [];

        foreach ($this->availableSlots as $slot) {
            if (($slot['day'] ?? '') === $dayName) {
                $slots[] = $slot;
            }
        }

        return $slots;
    }

    /**
     * Render individual time slots for a session
     * 
     * @param array    $slot       Availability slot data
     * @param DateTime $date       Date for these slots
     * @param string   $dateString Date in 'Y-m-d' format
     */
    private function renderTimeSlots($slot, $date, $dateString)
    {
        $sessionStart = new DateTime($slot['session_from']);
        $sessionEnd = new DateTime($slot['session_to']);
        $currentSlot = clone $sessionStart;

        // Generate slots from session start to session end
        while ($currentSlot < $sessionEnd) {
            $slotData = $this->prepareSlotData($currentSlot, $date, $dateString);
            $this->renderSlotCard($slotData);

            // Move to next slot
            $currentSlot->add($this->slotDuration);
        }
    }

    /**
     * Prepare data for a single time slot
     * 
     * @param DateTime $slotTime   Current slot time
     * @param DateTime $date       Date of the slot
     * @param string   $dateString Date string for comparison
     * @return array Slot data for rendering
     */
    private function prepareSlotData($slotTime, $date, $dateString)
    {
        // Clone to avoid modifying original
        $slotStart = clone $slotTime;
        $slotEnd = clone $slotTime;
        $slotEnd->add($this->slotDuration);

        return [
            'displayFrom' => $slotStart->format('h:i A'), // e.g., '09:00 AM'
            'displayTo' => $slotEnd->format('h:i A'),     // e.g., '09:30 AM'
            'dbFrom' => $slotStart->format('H:i:s'),      // e.g., '09:00:00'
            'dbTo' => $slotEnd->format('H:i:s'),          // e.g., '09:30:00'
            'date' => $dateString,
            'status' => $this->getSlotStatus($slotStart, $date, $dateString)
        ];
    }

    /**
     * Determine the status of a time slot
     * 
     * @param DateTime $slotTime   Slot start time
     * @param DateTime $date       Date of the slot
     * @param string   $dateString Date in string format
     * @return string Status: 'expired', 'available', or 'booked'
     */
    private function getSlotStatus($slotTime, $date, $dateString)
    {
        // Check if date is in the past
        if ($this->today > $date) {
            return 'expired';
        }

        // Check if slot is already booked
        $timeString = $slotTime->format('H:i:s');
        if ($this->isTimeSlotAvailable($timeString, $this->existingAppointments, $dateString)) {
            return 'available';
        }

        return 'booked';
    }

    /**
     * Render a single appointment slot card
     * 
     * @param array $slot Slot data including time, date, and status
     */
    private function renderSlotCard($slot)
    {
        echo '<div class="col-3">';
        echo '<div class="card m-3 mb-5">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">Appointment Slot</h5>';
        echo '<p class="card-text">' . $slot['displayFrom'] . ' to ' . $slot['displayTo'] . '</p>';

        // Render button based on slot status
        echo $this->renderSlotButton($slot);

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    /**
     * Render the appropriate button for a slot based on its status
     * 
     * @param array $slot Slot data
     * @return string HTML for the button
     */
    private function renderSlotButton($slot)
    {
        switch ($slot['status']) {
            case 'expired':
                return '<button disabled class="btn btn-warning">Expired</button>';

            case 'available':
                return sprintf(
                    '<button type="button" data-appointment-date="%s" data-time-slot-from="%s" data-time-slot-to="%s" class="btn btn-primary active book-modal" data-bs-toggle="modal" data-bs-target="#appointmentModal">Available</button>',
                    $slot['date'],
                    $slot['dbFrom'],
                    $slot['dbTo']
                );

            case 'booked':
            default:
                return '<button disabled class="btn btn-danger">Booked</button>';
        }
    }

    /**
     * Render message when no availability exists
     * 
     * @return string HTML message
     */
    private function renderNoAvailabilityMessage()
    {
        return '<h1 class="card p-3 text-danger text-center">No doctor availabilities.</h1>';
    }

    /**
     * Render doctor information header
     * 
     * Displays doctor's name, bio, and back navigation button
     *
     * @param array $doctor Doctor details from database
     * @return string HTML for the header section
     */
    private function renderDoctorHeader($doctor)
    {
        $doctorName = htmlspecialchars($doctor['name'] ?? 'Unknown Doctor');
        $doctorAbout = htmlspecialchars($doctor['about'] ?? '');
        $backUrl = url('views/admin/available_channelings.php');

        return '<div class="container">
                    <div class="row">
                        <div class="col-8">
                            <h2 class="mt-5">' . $doctorName . '</h2>
                            <h5 class="mb-3">' . $doctorAbout . '</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-group d-flex justify-content-end mx-5 my-5">
                                <a href="' . $backUrl . '" class="btn btn-dark active">Back</a>
                            </div>
                        </div>
                    </div>
                </div>';
    }



    /**
     * Check if a time slot is available (not booked)
     * 
     * Logic: A slot is available if it doesn't overlap with any existing appointment
     * on the same date.
     * 
     * Overlap occurs when: !(slotEnd <= apptStart OR slotStart >= apptEnd)
     * Simplified: NOT (slot ends before appointment starts OR slot starts after appointment ends)
     *
     * @param string $timeSlot             Time slot start (e.g., '09:00:00')
     * @param array  $existingAppointments All booked appointments
     * @param string $dateString           Date to check (e.g., '2025-01-15')
     * @return bool True if available, false if booked
     */
    private function isTimeSlotAvailable($timeSlot, $existingAppointments, $dateString)
    {
        // Calculate slot time range
        $slotStart = new DateTime($timeSlot);
        $slotEnd = clone $slotStart;
        $slotEnd->add($this->slotDuration);

        // Check each appointment for conflicts
        foreach ($existingAppointments as $appointment) {
            // Skip appointments on different dates
            if ($appointment['appointment_date'] !== $dateString) {
                continue;
            }

            // Get appointment time range
            $appointmentStart = new DateTime($appointment['time_slot_from']);
            $appointmentEnd = new DateTime($appointment['time_slot_to']);

            // Check for overlap using interval logic
            $hasOverlap = !($slotEnd <= $appointmentStart || $slotStart >= $appointmentEnd);

            if ($hasOverlap) {
                return false; // Slot conflicts with existing appointment
            }
        }

        return true; // No conflicts found, slot is available
    }

    /**
     * Get array of weekdays
     * 
     * @return array Days of the week in lowercase
     */
    private function getDays()
    {
        return [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday'
        ];
    }
}
