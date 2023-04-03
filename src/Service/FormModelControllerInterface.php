<?php

declare(strict_types=1);

namespace Fohn\Ui\Service;

/**
 * Form Model Controller Interface.
 *
 * @method getModel()
 */
interface FormModelControllerInterface
{
    /**
     * Save data model base on form controls value.
     * if id === null then a new record is created otherwise
     * the record corresponding to id is updated.
     */
    public function saveModelUsingForm(?string $id, array $formControls): array;

    /**
     * Return an array of form controls based on a data model.
     * Supplying id value will return control value set according to model.
     * If id === null then control value will be empty.
     */
    public function factoryFormControls(?string $id, array $fieldNames = []): array;

    /**
     * Return an array of key/value pair.
     * Key being the field name and
     * Value being format for html input.
     */
    public function getFormInputValue(string $id, array $fieldNames = []): array;
}
