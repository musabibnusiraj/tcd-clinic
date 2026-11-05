<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * Treatment - a dental treatment the clinic offers (table: treatments).
 */
class Treatment extends BaseModel
{
    public $name;
    public $description;
    public $registration_fee;
    public $treatment_fee;
    public $is_active;

    protected function getTableName()
    {
        return 'treatments';
    }

    protected function addNewRec()
    {
        return $this->pm->insert(
            'INSERT INTO treatments (name, description, treatment_fee, registration_fee, is_active)
             VALUES (:name, :description, :treatment_fee, :registration_fee, :is_active)',
            [
                ':name'             => $this->name,
                ':description'      => $this->description,
                ':treatment_fee'    => $this->treatment_fee,
                ':registration_fee' => $this->registration_fee,
                ':is_active'        => $this->is_active,
            ]
        );
    }

    protected function updateRec()
    {
        return $this->pm->execute(
            'UPDATE treatments
             SET name = :name,
                 description = :description,
                 treatment_fee = :treatment_fee,
                 registration_fee = :registration_fee,
                 is_active = :is_active
             WHERE id = :id',
            [
                ':name'             => $this->name,
                ':description'      => $this->description,
                ':treatment_fee'    => $this->treatment_fee,
                ':registration_fee' => $this->registration_fee,
                ':is_active'        => $this->is_active,
                ':id'               => $this->id,
            ]
        );
    }
}
