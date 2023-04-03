<?php

declare(strict_types=1);
/**
 * Interface for base model controller.
 *
 * @method getModel()
 */

namespace Fohn\Ui\Service;

interface ModelControllerInterface
{
    public function hasField(string $name): bool;

    public function getIdFieldName(): string;

    public function getTitleFieldName(): string;

    /**
     * @param mixed $id
     */
    public function getEntityValues($id): array;

    /**
     * @param mixed $id
     */
    public function delete($id): bool;

    public function getRows(array $fieldNames = [], int $limit = null): array;

    public function searchModel(string $query, array $searchFieldNames, int $limit = null): array;
}
