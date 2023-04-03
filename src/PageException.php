<?php

declare(strict_types=1);

/**
 * Render Exception to user.
 */

namespace Fohn\Ui;

class PageException extends Page
{
    public string $defaultTemplate = 'empty-page.html';
}
