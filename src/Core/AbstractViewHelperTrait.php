<?php

declare(strict_types=1);

/**
 * Helper for ContainerTrait in regard to AbstractView.
 * Add this to your AbstractView class.
 */

namespace Fohn\Ui\Core;

use Fohn\Ui\View;

trait AbstractViewHelperTrait
{
    /**
     * Add an abstract view to a view.
     *
     * @return static
     */
    public static function addAbstractTo(View $parent, array $properties = []) // :static
    {
        // @phpstan-ignore-next-line
        return $parent->addAbstractView(static::factory($properties));
    }
}
