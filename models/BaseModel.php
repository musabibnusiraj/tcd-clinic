<?php

require_once __DIR__ . '/../helpers/AppManager.php';

/**
 * BaseModel - the parent of every model (User, Doctor, Treatment ...).
 *
 * A "model" is a class that represents ONE database table.
 * This base class holds the code that every table needs (get all, get by id,
 * save, delete), so child classes only write what is different.
 *
 * To create a new model:
 *   1. extend BaseModel
 *   2. add one public property per table column
 *   3. implement getTableName(), addNewRec() and updateRec()
 */
abstract class BaseModel
{
    /** @var PersistanceManager Database manager shared by all models */
    protected $pm;

    // Columns that every table has
    public $id;
    public $created_at;
    public $updated_at;

    public function __construct()
    {
        $this->pm = AppManager::getPM();
    }

    /**
     * Name of the database table this model works with.
     */
    abstract protected function getTableName();

    /**
     * INSERT the current object into the table.
     * @return int The id of the new row
     */
    abstract protected function addNewRec();

    /**
     * UPDATE the row that has the current object's id.
     * @return int Number of rows changed
     */
    abstract protected function updateRec();

    // -----------------------------------------------------------------
    // Reading data
    // -----------------------------------------------------------------

    /**
     * Returns every row of the table.
     */
    public function getAll()
    {
        return $this->pm->fetchAll('SELECT * FROM ' . $this->getTableName());
    }

    /**
     * Returns every row where is_active = 1.
     */
    public function getAllActive()
    {
        return $this->pm->fetchAll('SELECT * FROM ' . $this->getTableName() . ' WHERE is_active = 1');
    }

    /**
     * Returns one row by its id (false when it does not exist).
     */
    public function getById($id)
    {
        return $this->pm->fetchOne(
            'SELECT * FROM ' . $this->getTableName() . ' WHERE id = :id',
            [':id' => $id]
        );
    }

    /**
     * Returns every row where $column equals $value.
     * Example: $appointment->getAllByColumnValue('doctor_id', 3);
     *
     * @throws InvalidArgumentException When the column name looks unsafe
     */
    public function getAllByColumnValue($column, $value)
    {
        // Column names cannot be sent as parameters, so only allow safe names
        if (!preg_match('/^[a-zA-Z_]+$/', $column)) {
            throw new InvalidArgumentException('Invalid column name.');
        }

        return $this->pm->fetchAll(
            'SELECT * FROM ' . $this->getTableName() . ' WHERE ' . $column . ' = :value',
            [':value' => $value]
        );
    }

    // -----------------------------------------------------------------
    // Writing data
    // -----------------------------------------------------------------

    /**
     * Saves the object: UPDATE when it has an id, otherwise INSERT.
     *
     * @return int New id (insert) or number of changed rows (update)
     */
    public function save()
    {
        if (!empty($this->id) && $this->id > 0) {
            return $this->updateRec();
        }
        return $this->addNewRec();
    }

    /**
     * Deletes one row by its id.
     *
     * @return int Number of rows deleted
     */
    public function deleteById($id)
    {
        return $this->pm->execute(
            'DELETE FROM ' . $this->getTableName() . ' WHERE id = :id',
            [':id' => $id]
        );
    }

    /**
     * Copies values from an array (e.g. $_POST) into this object's properties.
     *
     * Only the property names listed in $allowedFields are copied. This stops
     * a user from sending extra fields (like "id") that we did not expect.
     *
     * Example: $treatment->fill($_POST, ['name', 'description']);
     *
     * @param array $data          Source values (key = property name)
     * @param array $allowedFields Property names that may be copied
     * @return $this So calls can be chained
     */
    public function fill(array $data, array $allowedFields)
    {
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data) && property_exists($this, $field)) {
                $this->$field = $data[$field];
            }
        }
        return $this;
    }
}
