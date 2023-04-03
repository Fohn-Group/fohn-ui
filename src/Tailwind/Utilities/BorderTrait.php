<?php
/**
 * Border utility.
 */
declare(strict_types=1);

namespace Fohn\Ui\Tailwind\Utilities;

use Fohn\Ui\Tailwind\Tw;

trait BorderTrait
{
    public static function borderRadius(string $value = '', string $variant = ''): string
    {
        return Tw::utility('rounded', $value, $variant);
    }

    /**
     * Apply border width Tailwind utility.
     * Default to 1px border width.
     */
    public static function border(string $value = '', string $variant = ''): string
    {
        $propValue = $value === '1' ? '' : $value;

        return Tw::utility('border', $propValue, $variant);
    }

    public static function borderAt(string $position, string $value = '', string $variant = ''): string
    {
        $axis = Tw::POSITION_MAP[$position] ?? $position;

        return Tw::utility('border' . $axis, $value, $variant);
    }

    public static function borderColor(string $colorName, string $variant = ''): string
    {
        return Tw::colour($colorName, 'border', $variant);
    }

    public static function borderStyle(string $value, string $variant = ''): string
    {
        return Tw::utility('border', $value, $variant);
    }

    public static function divideColor(string $colorName, string $variant = ''): string
    {
        return Tw::colour($colorName, 'divide', $variant);
    }

    public static function ringColor(string $colorName, string $variant = ''): string
    {
        return Tw::colour($colorName, 'ring', $variant);
    }

    public static function ringOffsetColor(string $colorName, string $variant = ''): string
    {
        return Tw::colour($colorName, 'ring-offset', $variant);
    }
}
