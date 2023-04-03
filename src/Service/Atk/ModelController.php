<?php

declare(strict_types=1);
/**
 * Base Controller for an ATK model.
 */

namespace Fohn\Ui\Service\Atk;

use Atk4\Data\Exception;
use Atk4\Data\Model;
use Fohn\Ui\Service\ModelControllerInterface;
use Fohn\Ui\Service\Ui;

class ModelController implements ModelControllerInterface
{
    /** The Atk model. */
    private Model $model;

    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function hasField(string $name): bool
    {
        return $this->model->hasField($name);
    }

    public function getIdFieldName(): string
    {
        return $this->model->idField;
    }

    public function getTitleFieldName(): string
    {
        return $this->model->titleField;
    }

    /**
     * @param mixed $id
     */
    public function getEntityValues($id): array
    {
        return $this->model->load($id)->get();
    }

    /**
     * Return true on a successful delete.
     *
     * @param mixed $id
     */
    public function delete($id): bool
    {
        try {
            $m = $this->getModel()->delete($id);

            return !$m->tryLoad($id)->isLoaded();
        } catch (Exception $e) {
            if (Ui::service()->environment === Ui::DEV_ENV) {
                throw $e;
            }

            return false;
        }
    }

    public function getRows(array $fieldNames = [], int $limit = null): array
    {
        if ($limit) {
            $this->model->setLimit($limit);
        }

        return $this->model->export($fieldNames);
    }

    public function searchModel(string $query, array $searchFieldNames, int $limit = null): array
    {
        $scope = Model\Scope::createOr();
        foreach ($searchFieldNames as $fieldName) {
            $scope->addCondition($fieldName, 'like', '%' . $query . '%');
        }
        $this->getModel()->addCondition($scope);
        if ($limit) {
            $this->getModel()->setLimit($limit);
        }

        return $this->getModel()->export();
    }
}
