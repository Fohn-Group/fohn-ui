<?php

declare(strict_types=1);
/**
 * Manage Atk model for Table.
 */

namespace Fohn\Ui\Service\Atk;

use Atk4\Data\Model;
use Atk4\Data\Model\Scope;
use Atk4\Data\Model\Scope\Condition;
use Fohn\Ui\Component\Table\Filter\FilterOperators;
use Fohn\Ui\Component\Table\Payload;
use Fohn\Ui\Component\Table\Result\Set;
use Fohn\Ui\Service\TableModelControllerInterface;

class TableModelController extends ModelController implements TableModelControllerInterface
{
    /** a list of field name to be searched. */
    protected array $searchFields = [];

    /** Map filter operator to model scope. */
    protected array $filterOperatorMap = [
        FilterOperators::IS_ANY_OF => Condition::OPERATOR_IN,
        FilterOperators::IS => Condition::OPERATOR_EQUALS,
        FilterOperators::IS_NOT_EMPTY => Condition::OPERATOR_DOESNOT_EQUAL,
        FilterOperators::IS_EMPTY => Condition::OPERATOR_EQUALS,
        FilterOperators::NOT_EQUAL => Condition::OPERATOR_DOESNOT_EQUAL,
        FilterOperators::IS_NOT => Condition::OPERATOR_DOESNOT_EQUAL,
        FilterOperators::TEXT_EQUALS => Condition::OPERATOR_EQUALS,
        FilterOperators::TEXT_ENDS_WITH => Condition::OPERATOR_LIKE,
        FilterOperators::TEXT_NOT_ENDS_WITH => Condition::OPERATOR_NOT_LIKE,
        FilterOperators::TEXT_STARTS_WITH => Condition::OPERATOR_LIKE,
        FilterOperators::TEXT_NOT_STARTS_WITH => Condition::OPERATOR_NOT_LIKE,
        FilterOperators::TEXT_NOT_CONTAINS => Condition::OPERATOR_NOT_LIKE,
        FilterOperators::TEXT_CONTAINS => Condition::OPERATOR_LIKE,
        FilterOperators::IS_ON_OR_BEFORE => Condition::OPERATOR_LESS_EQUAL,
        FilterOperators::IS_ON_OR_AFTER => Condition::OPERATOR_GREATER_EQUAL,
        FilterOperators::IS_BEFORE => Condition::OPERATOR_LESS,
        FilterOperators::IS_AFTER => Condition::OPERATOR_GREATER,
        FilterOperators::LESS => Condition::OPERATOR_LESS,
        FilterOperators::LESS_EQUAL => Condition::OPERATOR_LESS_EQUAL,
        FilterOperators::GREATER => Condition::OPERATOR_GREATER,
        FilterOperators::GREATER_EQUAL => Condition::OPERATOR_GREATER_EQUAL,
        FilterOperators::EQUAL => Condition::OPERATOR_EQUALS,
        FilterOperators::IN => Condition::OPERATOR_IN,
        FilterOperators::NOT_IN => Condition::OPERATOR_NOT_IN,
        FilterOperators::LIKE => Condition::OPERATOR_LIKE,
        FilterOperators::NOT_LIKE => Condition::OPERATOR_NOT_LIKE,
    ];

    public function __construct(Model $model)
    {
        $this->setModel($model->isEntity() ? $model->getModel() : $model);
        $this->searchFields[] = $this->getModel()->titleField;
    }

    public function setSearchFields(array $fields): void
    {
        $this->searchFields = $fields;
    }

    public function getSearchFields(): array
    {
        return $this->searchFields;
    }

    public function setTableResultSet(Payload $payload, Set $resultSet): void
    {
        if ($payload->sortColumn) {
            $this->getModel()->setOrder($payload->sortColumn, $payload->sortDirection);
        }

        $filterScope = $this->filterToModelScope($payload->filters);
        if (!$filterScope->isEmpty()) {
            $this->getModel()->addCondition($filterScope);
        }

        if ($payload->searchQuery) {
            $searchScope = Scope::createOr();
            foreach ($this->getModel()->getFields() as $field) {
                if (in_array($field->shortName, $this->searchFields, true)) {
                    $searchScope->addCondition($field, 'like', '%' . $payload->searchQuery . '%');
                }
            }
            if (!$searchScope->isEmpty()) {
                $this->getModel()->addCondition($searchScope);
            }
        }

        $this->getModel()->setLimit($payload->ipp, ($payload->page - 1) * $payload->ipp);

        $resultSet->dataSet = $this->getModel()->export();
        $resultSet->totalItems = $this->getRecordCount();
    }

    protected function filterToModelScope(array $filters): Scope
    {
        $matchType = ($filters['matchType'] ?? 'and') === 'and' ? Scope::AND : Scope::OR;
        $columns = $filters['columns'] ?? [];

        // Get Condition for each column.
        $conditions = [];
        foreach ($columns as $column) {
            $conditions[] = $this->getScopeCondition($column);
        }

        return new Scope($conditions, $matchType);
    }

    protected function getScopeCondition(array $column): Scope\Condition
    {
        $key = $column['column'] ?? null;
        $operator = (string) ($column['operator'] ?? null);
        $value = $column['filterValue'] ?? null;

        switch ($operator) {
            case FilterOperators::IS_EMPTY:
            case FilterOperators::IS_NOT_EMPTY:
                $value = null;

                break;
            case FilterOperators::TEXT_STARTS_WITH:
            case FilterOperators::TEXT_NOT_STARTS_WITH:
                $value = $value . '%';

                break;
            case FilterOperators::TEXT_ENDS_WITH:
            case FilterOperators::TEXT_NOT_ENDS_WITH:
                $value = '%' . $value;

                break;
            case FilterOperators::TEXT_CONTAINS:
            case FilterOperators::TEXT_NOT_CONTAINS:
                $value = '%' . $value . '%';

                break;
            case FilterOperators::IN:
            case FilterOperators::NOT_IN:
                $value = explode($this->detectDelimiter($value), (string) $value);

                break;
            default:
                break;
        }

        // $operatorsMap = array_merge(...array_values(self::$operatorsMap));

        $operator = $operator ? ($this->filterOperatorMap[$operator] ?? '=') : null;

        return new Scope\Condition($key, $operator, $value);
    }

    private function detectDelimiter(string $value): string
    {
        $delimiters = [';', ',', '.'];
        $matches = [];
        foreach ($delimiters as $delimiter) {
            $matches[$delimiter] = substr_count((string) $value, $delimiter);
        }

        $max = array_keys($matches, max($matches), true);

        return reset($max) ?: reset($delimiters);
    }

    private function getRecordCount(): int
    {
        return (int) $this->getModel()->action('count')->getOne();
    }
}
