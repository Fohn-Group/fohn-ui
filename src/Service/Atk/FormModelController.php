<?php

declare(strict_types=1);

/**
 * Atk Model controller.
 * Bridge between an Atk model and Ui view modelCtrl property.
 *
 * When View instances need to read or set model data, they should do so via a modelCrl.
 */

namespace Fohn\Ui\Service\Atk;

use Atk4\Data\Model;
use Atk4\Data\ValidationException;
use Fohn\Ui\Component\Form\Control;
use Fohn\Ui\Service\FormModelControllerInterface;

class FormModelController extends ModelController implements FormModelControllerInterface
{
    /** Will create form control based on model field definition. */
    private FormControlFactory $ctrlFactory;

    public function __construct(Model $model)
    {
        $this->setModel($model->isEntity() ? $model->getModel() : $model);
        $this->ctrlFactory = new FormControlFactory();
    }

    public function factoryFormControls(?string $id, array $fieldNames = []): array
    {
        $entity = $id ? $this->getModel()->load($id) : $this->getModel()->createEntity();

        $controls = [];
        $fields = [];
        if (!$fieldNames) {
            $fields = $this->getModel()->getFields('editable');
        } else {
            foreach ($fieldNames as $fieldName) {
                $fields[] = $this->getModel()->getField($fieldName);
            }
        }

        foreach ($fields as $field) {
            $ctrl = $this->ctrlFactory->fromField($field);
            $ctrl->setValue($field->get($entity));

            $controls[] = $ctrl;
        }

        return $controls;
    }

    /**
     * return an array of fieldName => value pair with value
     * formatted for input control.
     */
    public function getFormInputValue(string $id, array $fieldNames = []): array
    {
        $data = [];
        foreach ($this->factoryFormControls($id, $fieldNames) as $control) {
            /** @var Control $control */
            $data[$control->getControlName()] = $control->getInputValue();
        }

        return $data;
    }

    public function saveModelUsingForm(?string $id, array $formControls): array
    {
        $entity = $id ? $this->getModel()->load($id) : $this->getModel()->createEntity();

        $formErrors = [];
        $modelErrors = $this->setModelFields($formControls, $entity);

        try {
            $entity->save();
        } catch (ValidationException $e) {
            foreach ($e->errors as $fieldName => $errorMsg) {
                $modelErrors[$fieldName][] = $errorMsg;
            }
        } catch (\Fohn\Ui\Core\Exception $e) {
            throw $e;
        }

        foreach ($modelErrors as $fieldName => $error) {
            $formErrors[$fieldName] = implode(' / ', array_values($error));
        }

        return $formErrors;
    }

    /**
     * Set models field using Form control value.
     * Return an array of fieldName => errorMsg if setting field generated an error.
     */
    protected function setModelFields(array $formControls, Model $entity): array
    {
        $errors = [];
        foreach ($formControls as $name => $control) {
            try {
                if ($this->getModel()->hasField($name)) {
                    $field = $this->getModel()->getField($name);
                    $value = $control->getValue();
                    $field->set($entity, $value);
                }
            } catch (ValidationException $e) {
                $errors[$name][] = $e->getMessage();
            } catch (\Fohn\Ui\Core\Exception $e) {
                throw $e;
            }
        }

        return $errors;
    }
}
