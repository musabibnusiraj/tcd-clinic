<?php
$requireOperator = true; // Only operators can open this page
require_once('layouts/header.php');
require_once BASE_PATH . '/models/Doctor.php';

$doctors = (new Doctor())->getAll();
?>


<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Doctors

    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header">Doctors</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>About</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    foreach ($doctors as $key => $doctor) {
                    ?>
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= e($doctor['name'] ?? '') ?></strong></td>
                            <td><?= e($doctor['about'] ?? '') ?></td>
                            <td>
                                <?php if (!empty($doctor['photo'])) : ?>
                                    <img src="<?= asset('assets/uploads/' . $doctor['photo']) ?>" alt="user-avatar" class="d-block rounded m-3" width="80" id="uploadedAvatar">
                                <?php else : ?>
                                    <img src="<?= asset('assets/img/avatars/1.png') ?>" alt="user-avatar" class="d-block rounded m-3" width="80" id="uploadedAvatar">
                                <?php endif; ?>
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



<?php
require_once('layouts/footer.php');
?>