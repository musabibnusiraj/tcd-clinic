<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../helpers/ImageUploader.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Doctor.php';

/**
 * UserController - AJAX actions for the Users page.
 *
 * A user with the 'doctor' permission also gets a doctor profile
 * (name, about text, photo) stored in the doctors table.
 */
class UserController extends BaseController
{
    protected function routes()
    {
        return [
            'POST' => [
                'create_user' => 'create',
                'update_user' => 'update',
                'delete_user' => 'delete',
            ],
            'GET' => [
                'get_user' => 'get',
            ],
        ];
    }

    /**
     * Creates a user (and a doctor profile when the permission is 'doctor').
     */
    protected function create()
    {
        $username   = $this->input('name');
        $email      = $this->input('email');
        $permission = $this->input('permission');

        $this->validateUserFields($username, $email);
        $this->validatePermission($permission);

        $user = new User();
        if ($user->isUsernameOrEmailTaken($username, $email)) {
            JsonResponse::error('Failed to create user. May be user already exist!');
        }

        // Save the doctor's photo (null when no photo was chosen)
        $uploader = new ImageUploader(BASE_PATH . '/assets/uploads/');
        $photoFileName = $uploader->save($_FILES['image'] ?? null);

        // Create the user, then the doctor profile that points to it
        $user->username = $username;
        $user->email = $email;
        $user->password = $this->input('password');
        $user->permission = $permission;
        $user->is_active = 1;
        $userId = $user->save();

        if ($permission === PERMISSION_DOCTOR) {
            (new Doctor())->createDoctor(
                $this->input('doctor_name'),
                $this->input('about_doctor'),
                $userId,
                $photoFileName
            );
        }

        JsonResponse::success('User created successfully!');
    }

    /**
     * Updates a user (and the doctor details when the permission is 'doctor').
     */
    protected function update()
    {
        $id         = $this->input('id');
        $username   = $this->input('user_name');
        $email      = $this->input('email');
        $permission = $this->input('permission', PERMISSION_DOCTOR);

        $this->validateUserFields($username, $email);
        $this->validatePermission($permission);

        $user = new User();
        if ($user->isUsernameOrEmailTaken($username, $email, $id)) {
            JsonResponse::error('Failed to update user. May be user already exist!');
        }

        $user->id = $id;
        $user->username = $username;
        $user->email = $email;
        $user->password = $this->input('password');
        $user->permission = $permission;
        $user->is_active = ($this->input('is_active') == 1) ? 1 : 0;
        $user->save();

        if ($permission === PERMISSION_DOCTOR) {
            (new Doctor())->updateDetails(
                $this->input('doctor_id'),
                $this->input('doctor_name'),
                $this->input('about_doctor')
            );
        }

        JsonResponse::success('User updated successfully!');
    }

    /**
     * Deletes a user. A doctor with appointments cannot be deleted.
     */
    protected function delete()
    {
        $userId = $this->input('user_id');

        // Safety: nobody can delete the account they are logged in with
        if ($userId == Auth::userId()) {
            JsonResponse::error('You cannot delete your own account.');
        }

        // Remove the doctor profile first (returns false when it has appointments)
        if (!(new Doctor())->deleteDoctorByUserId($userId)) {
            JsonResponse::error('Doctor has appointments and cannot be deleted.');
        }

        (new User())->deleteById($userId);

        JsonResponse::success('User deleted successfully!');
    }

    /**
     * Returns one user with the doctor details (used to fill the edit form).
     */
    protected function get()
    {
        $user = (new User())->getUserWithDoctorById($this->input('user_id'));

        if (!$user) {
            JsonResponse::error('User not found.');
        }

        JsonResponse::success('User data retrieved successfully!', $user);
    }

    // -----------------------------------------------------------------
    // Validation helpers (shared by create() and update())
    // -----------------------------------------------------------------

    /**
     * Checks the fields that both the create and the update form have.
     *
     * @throws InvalidArgumentException With the message to show to the user
     */
    private function validateUserFields($username, $email)
    {
        $this->requireFields(['password', 'confirm_password']);

        if (empty($username) || empty($email)) {
            throw new InvalidArgumentException('Required fields are missing!');
        }

        if ($this->input('password') !== $this->input('confirm_password')) {
            throw new InvalidArgumentException('Passwords do not match..!');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address');
        }
    }

    /**
     * Only the known permission names are accepted.
     *
     * @throws InvalidArgumentException
     */
    private function validatePermission($permission)
    {
        if (!in_array($permission, [PERMISSION_OPERATOR, PERMISSION_DOCTOR], true)) {
            throw new InvalidArgumentException('Invalid permission.');
        }
    }
}
