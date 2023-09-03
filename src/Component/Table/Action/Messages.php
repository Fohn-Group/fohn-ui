<?php

declare(strict_types=1);
/**
 * User messages that display within a confirmation dialog
 * when a table action is to be executed.
 * Default placeholder for user selections count is {#}.
 */

namespace Fohn\Ui\Component\Table\Action;

use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsRenderInterface;

class Messages
{
    /** Message value to display when no row are selected. */
    public string $none = '';
    /** Message value to display when only one row is selected. */
    public string $single = '';
    /** Message value to display when more than one row is selected. */
    public string $multiple = '';

    public function getJsMessages(): JsRenderInterface
    {
        return Js::object(['none' => $this->none, 'single' => $this->single, 'multiple' => $this->multiple]);
    }
}
