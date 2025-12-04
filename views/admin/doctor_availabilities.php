<?php
require_once('layouts/header.php');
include BASE_PATH . '/models/DoctorAvailability.php';
//

//
// if (!isset($userId) && empty($userId)) dd('Access Denied...!');
?>


<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">


    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Doctor Availabilities

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
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    $doctorAvailabilitiesObj = new DoctorAvailability();
                    $availabilities = $doctorAvailabilitiesObj->getAll();

                    foreach ($availabilities as $key => $availability) {
                    ?>
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong class="text-capitalize"><?= $availability['day'] ?? '' ?></strong></td>
                            <td><?= $availability['session_from'] ?? '' ?></td>
                            <td><?= $availability['session_to'] ?? '' ?></td>
                            <td><?= $availability['doctor_name'] ?? '' ?></td>
                            <td>
                                <span class="badge bg-<?= $availability['is_active'] == 1 ? 'success' : 'danger'; ?>">
                                    <?= $availability['is_active'] == 1 ? 'Active' : ' Inactive'; ?>
                                </span>
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

<!-- Create Modal -->
<div class="modal fade" id="createTreatment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="create-form" action="<?= url('services/treatment/ajax_fn.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Add New Treatment</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_treatment">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Name</label>
                            <input
                                type="text"
                                required
                                id="name"
                                name="name"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Description</label>
                            <input
                                type="text"
                                required
                                id="description"
                                name="description"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Treatment Fee</label>
                            <input
                                type="number"
                                required
                                id="treatment_fee"
                                name="treatment_fee"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Registration Fee</label>
                            <input
                                type="number"
                                required
                                id="registration_fee"
                                name="registration_fee"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Status</label>
                            <select class="form-select" id="is_active" aria-label="Default select example" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <div id="alert-container"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="create">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="edit-treatment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="update-form" action="<?= url('services/treatment/ajax_fn.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Update Treatment</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_treatment">
                    <input type="hidden" name="id" value="" id="id">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Name</label>
                            <input
                                type="text"
                                required
                                id="edit_name"
                                name="name"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Description</label>
                            <input
                                type="text"
                                required
                                id="edit_description"
                                name="description"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Treatment Fee</label>
                            <input
                                type="number"
                                required
                                id="edit_treatment_fee"
                                name="treatment_fee"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Registration Fee</label>
                            <input
                                type="number"
                                required
                                id="edit_registration_fee"
                                name="registration_fee"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Status</label>
                            <select class="form-select" id="edit_is_active" aria-label="Default select example" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <div id="edit-alert-container"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="update-treatment">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this treatment?</p>
                <div id="delete-alert-container"></div>
                <!-- hidden fields to store id and permission so JS can read them -->
                <input type="hidden" id="delete_treatment_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>


<?php
require_once('layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/treatments.js') ?>"></script>