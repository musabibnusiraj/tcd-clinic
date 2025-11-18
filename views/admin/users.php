<?php
require_once('layouts/header.php');
include BASE_PATH . '/models/User.php';


?>


<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Dashboard /</span> Users
        <!-- Button trigger modal -->
        <button
            type="button"
            class="btn btn-primary float-end"
            data-bs-toggle="modal"
            data-bs-target="#createNewUser">
            Add New User
        </button>
    </h4>

    <div class="card">
        <div>
            <h5 class="card-header">Users</h5>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    $userObj = new User();
                    $users = $userObj->getAll();
                    //
                    foreach ($users as $key => $user) {
                    ?>
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $user['username'] ?? 'Unknown' ?></strong></td>
                            <td><?= $user['email'] ?? 'Unknown' ?></td>
                            <td>
                                <span class="text-capitalize">
                                    <?= $user['permission'] ?? 'Unknown' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $user['is_active'] == 1 ? 'success' : 'danger'; ?>"><?= $user['is_active'] == 1 ? 'Active' : ' Inactive'; ?></span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                        <a class="dropdown-item delete-user-btn" data-permission="<?= $user['permission']; ?>" data-id="<?= $user['id']; ?>"><i class="bx bx-trash me-1"></i> Delete</a>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- / Content -->

<!-- Modal -->
<div class="modal fade" id="createNewUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="create-form" action="<?= url('services/user/ajax_fn.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add New User</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_user">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input
                                type="text"
                                required
                                id="nameWithTitle"
                                name="name"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="text"
                                required
                                id="email"
                                name="email"
                                class="form-control"
                                placeholder="Enter Email" />
                        </div>
                    </div>

                    <div class="row gy-2">
                        <div class="col orm-password-toggle">
                            <label class="form-label" for="basic-default-password1">Password</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    required
                                    name="password"
                                    class="form-control"
                                    id="passwordInput"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="basic-default-password1" />
                                <span id="basic-default-password1" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                        <div class="col form-password-toggle">
                            <label class="form-label" for="basic-default-password2">Confirm Password</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    required
                                    name="confirm_password"
                                    class="form-control"
                                    id="confirmPasswordInput"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="basic-default-password2" />
                                <span id="basic-default-password2" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Role</label>
                            <select class="form-select" id="permission" aria-label="Roles" name="permission" required>
                                <option value="operator">Operator</option>
                                <option value="doctor">Doctor</option>
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
<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user?</p>
                <div id="delete-alert-container"></div>
                <!-- hidden fields to store id and permission so JS can read them -->
                <input type="hidden" id="delete_user_id" value="">
                <input type="hidden" id="delete_user_permission" value="">
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
<script src="<?= asset('assets/forms-js/users.js') ?>"></script>