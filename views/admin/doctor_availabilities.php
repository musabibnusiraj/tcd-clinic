<?php
$requireOperator = true; // Only operators can open this page
require_once('layouts/header.php');
require_once BASE_PATH . '/models/DoctorAvailability.php';
require_once BASE_PATH . '/models/Doctor.php';

$availabilities = (new DoctorAvailability())->getAll();
$doctors = (new Doctor())->getAllActive();   // for the doctor drop-down in the form
?>


<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">


    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Doctor Availabilities
        <button type="button" class="btn btn-primary float-end" id="add-availability-btn">
            Add Availability
        </button>
    </h4>


    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header">Doctor Availabilities</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Doctor Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    foreach ($availabilities as $key => $availability) {
                    ?>
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong class="text-capitalize"><?= e($availability['day'] ?? '') ?></strong></td>
                            <td><?= e($availability['session_from'] ?? '') ?></td>
                            <td><?= e($availability['session_to'] ?? '') ?></td>
                            <td><?= e($availability['doctor_name'] ?? '') ?></td>
                            <td>
                                <span class="badge bg-<?= $availability['is_active'] == 1 ? 'success' : 'danger'; ?>">
                                    <?= $availability['is_active'] == 1 ? 'Active' : ' Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item edit-availability-btn" href="javascript:void(0);" data-id="<?= e($availability['id']) ?>"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                        <a class="dropdown-item delete-availability-btn" href="javascript:void(0);" data-id="<?= e($availability['id']) ?>"><i class="bx bx-trash me-1"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->

    <hr class="my-5" />


</div>

<!-- / Content -->

<!-- Add / Edit modal (one form for both) -->
<div class="modal fade" id="availability-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="availability-form" action="<?= url('services/availability/ajax_fn.php') ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="availability-modal-title">Add Availability</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- JavaScript sets the action (create_availability / update_availability) -->
                    <input type="hidden" name="action" id="availability_action" value="create_availability">
                    <input type="hidden" name="id" id="availability_id" value="">

                    <div class="mb-3">
                        <label for="availability_doctor" class="form-label">Doctor</label>
                        <select class="form-select" id="availability_doctor" name="doctor_id" required>
                            <option value="">Select Doctor</option>
                            <?php foreach ($doctors as $doctor) : ?>
                                <option value="<?= e($doctor['id']) ?>"><?= e($doctor['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="availability_day" class="form-label">Day</label>
                        <select class="form-select" id="availability_day" name="day" required>
                            <?php foreach (DoctorAvailability::DAYS as $day) : ?>
                                <option value="<?= e($day) ?>"><?= e(ucfirst($day)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="availability_from" class="form-label">From</label>
                            <input type="time" class="form-control" id="availability_from" name="session_from" required />
                        </div>
                        <div class="col mb-3">
                            <label for="availability_to" class="form-label">To</label>
                            <input type="time" class="form-control" id="availability_to" name="session_to" required />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="availability_is_active" class="form-label">Status</label>
                        <select class="form-select" id="availability_is_active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">In Active</option>
                        </select>
                    </div>

                    <div id="availability-alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save-availability">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="availability-delete-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this availability? Appointments that are already booked are not affected.</p>
                <div id="delete-alert-container"></div>
                <input type="hidden" id="delete_availability_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-availability">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once('layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/availabilities.js') ?>"></script>
