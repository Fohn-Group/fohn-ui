<?php
/**
 * Background.
 */
declare(strict_types=1);

namespace Fohn\Ui\Tailwind\Utilities;

use Fohn\Ui\Tailwind\Tw;

trait BackgroundTrait
{
    public static function bgColor(string $colorName, string $variant = ''): string
    {
        return Tw::colour($colorName, 'bg', $variant);
    }
}
