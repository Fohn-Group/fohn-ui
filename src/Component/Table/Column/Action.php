<?php

declare(strict_types=1);
/**
 * Represent a Js action column.
 */

namespace Fohn\Ui\Component\Table\Column;

use Fohn\Ui\Component\Table;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\Js\Type\ObjectLiteral;
use Fohn\Ui\View;

class Action extends Generic implements ActionInterface
{
    public const HOOK_DISABLED = self::class . '@action:enable';

    public string $defaultTemplate = 'vue-component/table/column/action.html';

    /** @var array<string, JsFunction> Column action may contain more than one row action as js function. */
    protected array $rowActions = [];

    public function getRowActions(): array
    {
        return $this->rowActions;
    }

    public function renderInTableTemplate(Table $table): void
    {
        parent::renderInTableTemplate($table);

        $table->getTemplate()->setJs('tableRowActions', ObjectLiteral::set($this->rowActions));
    }

    protected function beforeHtmlRender(): void
    {
        $this->clearIdAttribute($this);
        parent::beforeHtmlRender();
    }

    /**
     * Callback function in order to determine if a row action button should be disabled or not.
     * The closure function will receive to record of the row being evaluated.
     * The closure function should return true if disabling is need.
     *
     * Suppose you have a column name 'action' that contains an action name call 'edit' and
     * you would like to disable the action base on row value.
     * You can disable the button assign to the action name 'edit' this way:
     * $table->getTableColumn('action')->disableActionName('edit', static function ($rowValue) {
     *      return true or false;
     * });
     */
    public function disableActionName(string $actionName, \Closure $fx): self
    {
        $this->onHook(static::HOOK_DISABLED, static function (string $name, array $rowValue) use ($actionName, $fx): bool {
            $return = false;
            if ($actionName === $name) {
                $return = $fx($rowValue);
            }

            return $return;
        });

        return $this;
    }

    public function addRowActionFn(string $name, JsFunction $fn): JsFunction
    {
        $this->rowActions[$name] = $fn;

        return $fn;
    }

    /**
     * Action column content is duplicate within each row of a table,
     * therefore, make sure that each Views inside column has the Id
     * attribute set to ''.
     */
    private function clearIdAttribute(View $view): void
    {
        foreach ($view->getViewElements() as $v) {
            $v->setIdAttribute('');
            $this->clearIdAttribute($v);
        }
    }
}
