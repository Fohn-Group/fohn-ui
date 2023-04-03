<?php

declare(strict_types=1);
/**
 * Modal View.
 */

namespace Fohn\Ui\Component;

use Fohn\Ui\Callback\Generic;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsChain;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Js\Type\Type;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\View;
use Fohn\Ui\View\Button;

class Modal extends View implements VueInterface
{
    use VueTrait;

    private const JS_OPEN_OPTIONS = ['message', 'args', 'payload'];

    private const COMP_NAME = 'fohn-modal';
    private const PINIA_PREFIX = '__modal_';

    public string $defaultTemplate = '/vue-component/modal.html';

    protected string $title;

    protected bool $isClosable = true;

    protected ?Generic $cb = null;

    public View $content;
    public View $remoteContent;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        $this->content = View::addTo($this);
        $this->remoteContent = View::addTo($this, [], 'remoteContent');
    }

    public function addCloseButton(Button $closeBtn): self
    {
        $this->addView($closeBtn, 'Buttons');
        Ui::bindVueEvent($closeBtn, 'click', 'closeModal');

        return $this;
    }

    public function onOpen(\Closure $fx): void
    {
        $this->cb = Generic::addAbstractTo($this);

        $this->cb->onRequest(function () use ($fx) {
            $fx($this->remoteContent);
            $this->cb->terminateJson($this->remoteContent->renderToJsonArr());
        });
    }

    public function addContent(View $view): View
    {
        return $this->content->addView($view);
    }

    /**
     * Open Modal using modalStore function.
     * Available option to pass on jsOpen method.
     * message => text to display in Modal;
     * args => url GET arguments to include when fetching Html content;
     * payload => Payload request to include in all onCallbackEvent (AsDialog modal).
     */
    public function jsOpen(array $options = []): JsRenderInterface
    {
        if (!Ui::service()->hasValidOptions($options, self::JS_OPEN_OPTIONS)) {
            throw (new Exception('Invalid option pass to Modal::jsOpen() method'))
                ->addMoreInfo(
                    'Valid options are: ',
                    implode('/', self::JS_OPEN_OPTIONS)
                );
        }

        return JsChain::withUiLibrary()->store()->getModalStore($this->getPiniaStoreId(self::PINIA_PREFIX))->openModal(Js::object($options));
    }

    public function jsClose(): JsRenderInterface
    {
        return JsChain::withUiLibrary()->store()->getModalStore($this->getPiniaStoreId(self::PINIA_PREFIX))->closeModal();
    }

    public function jsSetTitle(string $title): JsRenderInterface
    {
        $js = JsChain::withUiLibrary()->store()->getModalStore($this->getPiniaStoreId(self::PINIA_PREFIX))->setTitle($title);
        $this->content->appendJsAction($js);

        return $js;
    }

    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->trySetJs('storeId', Type::factory($this->getPiniaStoreId(self::PINIA_PREFIX)));
        $this->getTemplate()->trySetJs('title', Type::factory($this->title));
        $this->getTemplate()->trySetJs('isClosable', Type::factory($this->isClosable));
        if ($this->cb) {
            $this->getTemplate()->trySetJs('contentUrl', Type::factory($this->cb->getUrl()));
        }

        $this->createVueApp(self::COMP_NAME, [], $this->getDefaultSelector());

        parent::beforeHtmlRender();
    }
}
