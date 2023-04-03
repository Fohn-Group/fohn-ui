<?php

declare(strict_types=1);

/**
 * Select component.
 */

namespace Fohn\Ui\Component\Form\Control;

use Fohn\Ui\Callback\Data;
use Fohn\Ui\Component\Form\Response\Items;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\Core\HookFn;
use Fohn\Ui\Core\HookTrait;
use Fohn\Ui\Js\Type\Type;

class Select extends Selection
{
    use HookTrait;

    public const COMP_NAME = 'fohnSelect';
    public const SEARCH_MODE = 'search';
    public const QUERY_MODE = 'query';

    public const HOOKS_GET_ITEMS = self::class . '@onRequestItems';
    public const HOOKS_QUERY_ITEMS = self::class . '@onQueryItems';

    public string $defaultTemplate = 'vue-component/form/control/select.html';

    public int $maxItems = 1000;
    protected string $filterMode = self::SEARCH_MODE;

    protected bool $allowNull = true;

    protected string $openIcon = 'bi bi-caret-down';
    protected string $closeIcon = 'bi bi-caret-up';

    protected ?Data $dataRequestCb = null;

    public function onItemsRequest(\Closure $fx): self
    {
        $this->initDataCallbackRequest();
        $this->onHooks(self::HOOKS_GET_ITEMS, $fx);

        return $this;
    }

    public function onQueryItems(\Closure $fx): self
    {
        $this->initDataCallbackRequest();
        $this->onHooks(self::HOOKS_QUERY_ITEMS, $fx);

        return $this;
    }

    /**
     * Determine how component will filter list items.
     * When:
     * null : No filtering;
     * SEARCH_MODE: Filter items internally;
     * QUERY_MODE: Filter items by querying server.
     */
    public function setFilterMode(string $mode): self
    {
        if (!($mode === self::QUERY_MODE || $mode === self::SEARCH_MODE)) {
            throw (new Exception('This filter mode is not allowed'))->addMoreInfo('filterMode', $mode);
        }

        $this->filterMode = $mode;

        return $this;
    }

    public function getFilterMode(): string
    {
        return $this->filterMode;
    }

    protected function beforeHtmlRender(): void
    {
        $this->outputDataRequest();

        $this->getTemplate()->trySetJs('items', Type::factory($this->getItems()));
        $this->getTemplate()->trySetJs('maxItems', Type::factory($this->maxItems));
        $this->getTemplate()->trySetJs('allowNull', Type::factory($this->allowNull));
        $this->getTemplate()->trySetJs('openIcon', Type::factory($this->openIcon));
        $this->getTemplate()->trySetJs('closeIcon', Type::factory($this->closeIcon));
        $this->getTemplate()->trySetJs('filterMode', Type::factory($this->filterMode));
        $this->getTemplate()->trySetJs('requestUrl', Type::factory($this->dataRequestCb ? $this->dataRequestCb->getUrl() : null));

        parent::beforeHtmlRender();
    }

    private function initDataCallbackRequest(): void
    {
        if (!$this->dataRequestCb) {
            $this->dataRequestCb = Data::addAbstractTo($this);
        }
    }

    /**
     * Call Hooks when dataRequestCb is trigger.
     * A $response instance is
     * pass as parameter to each Hook Closure function.
     * Ex:
     * $control->onItemsRequest(function($response, $value) {
     *      // Get items array
     *      $response->setItems($items)
     * });.
     *
     * $control->onQueryItems(function($response, $query) {
     *      // Get items array based on $query
     *      $response->setItems($items)
     * });
     */
    private function outputDataRequest(): void
    {
        if ($this->dataRequestCb) {
            $this->dataRequestCb->onDataRequest(function (array $payload) {
                $response = new Items();
                $value = $payload['value'] ?? null;
                $action = $payload['action'] ?? null;
                $query = $payload[self::COMP_NAME . '_q'] ?? '';

                switch ($action) {
                    case 'init':
                        // in case of init, add the $payload control value as parameter to !Closure function.
                        $this->callHooks(self::HOOKS_GET_ITEMS, HookFn::withVoid([$response, (string) $value]));

                        break;
                    case 'query':
                        // in case of init, add the $payload query value as parameter to !Closure function.
                        $this->callHooks(self::HOOKS_QUERY_ITEMS, HookFn::withVoid([$response, $query]));

                        break;
                    default:
                }

                return $response->getResponse();
            });
        }
    }
}
