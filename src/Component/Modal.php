<?php

declare(strict_types=1);
/**
 * Modal View.
 */

namespace Fohn\Ui\Component;

use Fohn\Ui\Callback\Generic;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Js\Type\Type;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
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

//    public ?View $content = null;

    public array $defaultModalTws = [
        'relative',
        'top-20',
        'mx-auto',
        'border',
        'shadow-lg',
        'rounded-md',
        'bg-white',
        'overflow-auto',
    ];

    public array $modalTwsWidth = ['w-10/12', 'md:w-4/6', 'lg:w-1/2'];

    // w-10/12 md:w-4/6 lg:w-1/2
    protected function initRenderTree(): void
    {
        parent::initRenderTree();
//        $this->content = View::addTo($this);
    }

    public function addCloseButton(Button $closeBtn): self
    {
        $this->addView($closeBtn, 'Buttons');
        Ui::bindVueEvent($closeBtn, 'click', 'closeModal');

        return $this;
    }

    public function addContent(View $view, string $region = self::MAIN_TEMPLATE_REGION): View
    {

        return $this->addView($view, $region);
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
        // @phpstan-ignore-next-line
        return $this->jsGetStore(self::PINIA_PREFIX)->openModal(Js::object($options));
    }

    public function jsClose(): JsRenderInterface
    {
        // @phpstan-ignore-next-line
        return $this->jsGetStore(self::PINIA_PREFIX)->closeModal();
    }

    public function jsSetTitle(string $title): JsRenderInterface
    {
        // @phpstan-ignore-next-line
        $js = $this->jsGetStore(self::PINIA_PREFIX)->setTitle($title);
        $this->content->appendJsAction($js);

        return $js;
    }

    protected function beforeHtmlRender(): void
    {
        $this->getTemplate()->trySet('modalClassAttr', Tw::from($this->defaultModalTws)->merge($this->modalTwsWidth)->toString());
        $this->getTemplate()->trySetJs('storeId', Type::factory($this->getPiniaStoreId(self::PINIA_PREFIX)));
        $this->getTemplate()->trySetJs('title', Type::factory($this->title));
        $this->getTemplate()->trySetJs('isClosable', Type::factory($this->isClosable));

        $this->createVueApp(self::COMP_NAME, [], $this->getDefaultSelector());

        parent::beforeHtmlRender();
    }
}
