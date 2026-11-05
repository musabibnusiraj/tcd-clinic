<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * User - a person who can log in (table: users).
 * The permission is either 'operator' (admin) or 'doctor'.
 *
 * The password is stored as a hash (never as plain text).
 */
class User extends BaseModel
{
    public $username;
    public $email;
    public $password;      // Plain text while saving; the database only gets the hash
    public $permission;
    public $is_active;

    protected function getTableName()
    {
        return 'users';
    }

    /**
     * Inserts a new user. The password is hashed before it is stored.
     *
     * @return int The new user's id
     */
    protected function addNewRec()
    {
        return $this->pm->insert(
            'INSERT INTO users (username, password, permission, email, is_active)
             VALUES (:username, :password, :permission, :email, :is_active)',
            [
                ':username'   => $this->username,
                ':password'   => password_hash($this->password, PASSWORD_DEFAULT),
                ':permission' => $this->permission,
                ':email'      => strtolower($this->email),
                ':is_active'  => $this->is_active,
            ]
        );
    }

    /**
     * Updates a user. The password is only changed when a new one was given.
     */
    protected function updateRec()
    {
        $sql = 'UPDATE users
                SET username = :username,
                    permission = :permission,
                    email = :email,
                    is_active = :is_active';

        $params = [
            ':username'   => $this->username,
            ':permission' => $this->permission,
            ':email'      => strtolower($this->email),
            ':is_active'  => $this->is_active,
            ':id'         => $this->id,
        ];

        // Add the password column only when a new password was typed
        if (!empty($this->password)) {
            $sql .= ', password = :password';
            $params[':password'] = password_hash($this->password, PASSWORD_DEFAULT);
        }

        return $this->pm->execute($sql . ' WHERE id = :id', $params);
    }

    /**
     * True when another user already uses this username or email.
     *
     * @param int|null $excludeUserId Ignore this user (use it when editing a user)
     */
    public function isUsernameOrEmailTaken($username, $email, $excludeUserId = null)
    {
        $sql = 'SELECT id FROM users WHERE (username = :username OR email = :email)';
        $params = [':username' => $username, ':email' => strtolower($email)];

        if ($excludeUserId !== null) {
            $sql .= ' AND id != :exclude_id';
            $params[':exclude_id'] = $excludeUserId;
        }

        return (bool) $this->pm->fetchOne($sql, $params);
    }

    /**
     * Finds a user by email (used by the login page).
     *
     * @return array|false The user row, or false when not found
     */
    public function getByEmail($email)
    {
        return $this->pm->fetchOne(
            'SELECT * FROM users WHERE email = :email',
            [':email' => strtolower($email)]
        );
    }

    /**
     * Returns a user together with the doctor profile (if the user is a doctor).
     */
    public function getUserWithDoctorById($id)
    {
        return $this->pm->fetchOne(
            'SELECT users.id, users.username, users.email, users.is_active, users.permission,
                    doctors.id AS doctor_id, doctors.name, doctors.about, doctors.photo,
                    doctors.is_active AS doctor_is_active
             FROM users
             LEFT JOIN doctors ON doctors.user_id = users.id
             WHERE users.id = :id',
            [':id' => $id]
        );
    }
}
