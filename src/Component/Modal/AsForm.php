<?php

declare(strict_types=1);
/**
 * A Modal component with a Form.
 * Form submit button is place on the modal button bar.
 */

namespace Fohn\Ui\Component\Modal;

use Fohn\Ui\Component\Form;
use Fohn\Ui\Component\Modal;
use Fohn\Ui\Js\JsRenderInterface;

class AsForm extends Modal
{
    protected Form $form;

    /**
     * Add a form to this modal.
     * Modal will get form button at modal buttons regions.
     */
    public function addForm(Form $form): Form
    {
        $this->form = $form;
        $this->addContent($this->form);
        /** @var Form\Layout\Standard $layout */
        $layout = $this->form->getLayout();
        $layout->appendTailwinds(['m-4']);
        $layout->addSubmitButton(false);
        $this->addView($this->form->getSubmitButton(), 'Buttons');
        $this->form->getSubmitButton()->appendHtmlAttribute('form', $this->form->getId());

        return $this->form;
    }

    /**
     * Open modal and request Form controls value with a specific id.
     */
    public function jsOpenWithId(?JsRenderInterface $id, array $options = []): array
    {
        return [
            $this->jsOpen($options),
            $id ? $this->form->jsRequestControlsValue($id) : $this->form->jsClearControlsValue(),
        ];
    }
}
