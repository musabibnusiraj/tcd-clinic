<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Treatment.php';

/**
 * TreatmentController - AJAX actions for the Treatments page.
 */
class TreatmentController extends BaseController
{
    /** Request fields that belong to a treatment */
    const FIELDS = ['name', 'description', 'registration_fee', 'treatment_fee', 'is_active'];

    protected function routes()
    {
        return [
            'POST' => [
                'create_treatment' => 'create',
                'update_treatment' => 'update',
            ],
            'GET' => [
                'get_treatment'    => 'get',
                'delete_treatment' => 'delete',
            ],
        ];
    }

    /**
     * Creates a new treatment.
     */
    protected function create()
    {
        $this->requireFields(['name'], 'Treatment name is required!');

        $treatment = new Treatment();
        $treatment->fill($this->only(self::FIELDS), self::FIELDS);
        $treatment->save();

        JsonResponse::success('Treatment created successfully!');
    }

    /**
     * Updates an existing treatment.
     */
    protected function update()
    {
        $this->requireFields(['id', 'name'], 'Treatment id and name are required!');

        $treatment = new Treatment();
        $treatment->fill($this->only(self::FIELDS), self::FIELDS);
        $treatment->id = $this->input('id');
        $treatment->save();

        JsonResponse::success('Treatment updated successfully!');
    }

    /**
     * Returns one treatment (used to fill the edit form).
     */
    protected function get()
    {
        $treatment = (new Treatment())->getById($this->input('id'));

        if (!$treatment) {
            JsonResponse::error('Treatment not found.');
        }

        JsonResponse::success('Treatment data retrieved successfully!', $treatment);
    }

    /**
     * Deletes a treatment.
     */
    protected function delete()
    {
        try {
            (new Treatment())->deleteById($this->input('id'));
        } catch (PDOException $e) {
            // SQLSTATE 23000 = foreign key problem: appointments use this treatment
            if ($e->getCode() === '23000') {
                JsonResponse::error('This treatment is used by appointments and cannot be deleted.');
            }
            throw $e;
        }

        JsonResponse::success('Treatment deleted successfully!');
    }
}
