<?php

declare(strict_types=1);
/**
 * Input test purpose.
 */

namespace Fohn\Ui\Tests\Views;

use Fohn\Ui\Component\Form\Control\Input;

class MockInput extends Input
{
    public function getHtmlAttrs(): array
    {
        return $this->inputAttrs;
    }
}
