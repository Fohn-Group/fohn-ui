<?php

declare(strict_types=1);
/**
 * Modal that display content from a callback request.
 */

namespace Fohn\Ui\Component\Modal;

use Fohn\Ui\Callback\Generic;
use Fohn\Ui\Component\Modal;
use Fohn\Ui\Js\Type\Type;
use Fohn\Ui\View;

class AsDynamic extends Modal
{
    protected ?Generic $cb = null;
    protected View $dynamicContent;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        $this->dynamicContent = View::addTo($this);
    }

    public function onOpen(\Closure $fx): void
    {
        $this->cb = Generic::addAbstractTo($this);

        $this->cb->onRequest(function () use ($fx) {
            $fx($this->dynamicContent);
            $this->cb->terminateJson($this->dynamicContent->renderToJsonArr());
        });
    }

    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->trySetJs('contentUrl', Type::factory($this->cb->getUrl()));

        parent::beforeHtmlRender();
    }
}
