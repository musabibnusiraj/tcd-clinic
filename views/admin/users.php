<?php
require_once('layouts/header.php');
include BASE_PATH . '/models/User.php';



// if (!isset($userId) && empty($userId)) dd('Access Denied...!');

?>


<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <h5 class="card-header">Users</h5>
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
                                        <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>
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



<?php
require_once('layouts/footer.php');
?>