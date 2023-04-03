<?php

declare(strict_types=1);

/**
 * Generic Tailwind Css utilities function.
 */

namespace Fohn\Ui\Tailwind\Utilities;

use Fohn\Ui\Tailwind\Tw;

trait GenericTrait
{
    // -------------
    // TYPOGRAPHY
    // -------------

    public static function textColor(string $colorName, string $variant = ''): string
    {
        return Tw::colour($colorName, 'text', $variant);
    }

    public static function textSize(string $size, string $variant = ''): string
    {
        return Tw::utility('text', $size, $variant);
    }

    public static function textAlign(string $position, string $variant = ''): string
    {
        return Tw::utility('text', $position, $variant);
    }

    public static function textVerticalAlign(string $position, string $variant = ''): string
    {
        return Tw::utility('align', $position, $variant);
    }

    // -------------
    // SPACING
    // -------------

    public static function margin(string $value, string $variant = '', bool $isNegatvive = false): string
    {
        return Tw::utility($isNegatvive ? '-m' : 'm', $value, $variant);
    }

    /**
     * Set margin using position.
     * Position is 'top', 'bottom' 'left', 'right', 'top-bottom' or 'left-right' inclusively.
     * isNegative will apply a negative margin value.
     */
    public static function marginAt(string $position, string $value, string $variant = '', bool $isNegative = false): string
    {
        $axis = Tw::POSITION_MAP[$position] ?? $position;
        $base = $isNegative ? '-m' : 'm';

        return Tw::utility($base . $axis, $value, $variant);
    }

    public static function marginY(string $value, string $variant = '', bool $isNegative = false): string
    {
        return self::marginAt('y', $value, $variant, $isNegative);
    }

    public static function marginX(string $value, string $variant = '', bool $isNegative = false): string
    {
        return self::marginAt('x', $value, $variant, $isNegative);
    }

    public static function padding(string $value, string $variant = '', bool $isNegatvive = false): string
    {
        return Tw::utility($isNegatvive ? '-p' : 'p', $value, $variant);
    }

    /**
     * Set padding using position.
     * Position is 'top', 'bottom' 'left', 'right', 'top-bottom' or 'left-right' inclusively.
     * isNegative will apply a negative padding value.
     */
    public static function paddingAt(string $position, string $value, string $variant = '', bool $isNegative = false): string
    {
        $axis = Tw::POSITION_MAP[$position] ?? $position;
        $base = $isNegative ? '-p' : 'p';

        return Tw::utility($base . $axis, $value, $variant);
    }

    public static function paddingY(string $value, string $variant = '', bool $isNegative = false): string
    {
        return self::paddingAt('y', $value, $variant, $isNegative);
    }

    public static function paddingX(string $value, string $variant = '', bool $isNegative = false): string
    {
        return self::paddingAt('x', $value, $variant, $isNegative);
    }

    // -------------
    // SIZING
    // -------------

    public static function width(string $width, string $variant = ''): string
    {
        return Tw::utility('w', $width, $variant);
    }

    public static function height(string $height, string $variant = ''): string
    {
        return Tw::utility('h', $height, $variant);
    }

    // -------------
    // BOX ALIGNMENT
    // -------------

    public static function placeContent(string $position, string $variant = ''): string
    {
        return Tw::utility('place-content', $position, $variant);
    }

    public static function placeItems(string $position, string $variant = ''): string
    {
        return Tw::utility('place-items', $position, $variant);
    }

    public static function placeSelf(string $position, string $variant = ''): string
    {
        return Tw::utility('place-self', $position, $variant);
    }

    public static function justifyContent(string $position, string $variant = ''): string
    {
        return Tw::utility('justify', $position, $variant);
    }

    public static function justifyItems(string $position, string $variant = ''): string
    {
        return Tw::utility('justify-items', $position, $variant);
    }

    public static function justifySelf(string $position, string $variant = ''): string
    {
        return Tw::utility('justify-self', $position, $variant);
    }

    public static function alignContent(string $position, string $variant = ''): string
    {
        return Tw::utility('content', $position, $variant);
    }

    public static function alignItems(string $position, string $variant = ''): string
    {
        return Tw::utility('items', $position, $variant);
    }

    public static function alignSelf(string $position, string $variant = ''): string
    {
        return Tw::utility('self', $position, $variant);
    }

    // -------------
    // GRID
    // -------------

//    public static function grid(): string
//    {
//        return Tw::utility('', '', 'grid');
//    }

    public static function gridType(string $type, string $size, string $variant = ''): string
    {
        return Tw::utility('grid', $type . '-' . $size, $variant);
    }

    public static function gridGap(string $value, string $variant = ''): string
    {
        return Tw::utility('gap', $value, $variant);
    }

    public static function gridGapAt(string $position, string $value, string $variant = ''): string
    {
        return Tw::utility('gap', $position . '-' . $value, $variant);
    }

    public static function gridAutoFlow(string $value, string $variant = ''): string
    {
        return Tw::utility('grid-flow', $value, $variant);
    }

    public static function gridCol(string $utility, string $value = '', string $variant = ''): string
    {
        $compValue = $value ? $utility . '-' . $value : $utility;

        return Tw::utility('col', $compValue, $variant);
    }

    public static function gridRow(string $utility, string $value = '', string $variant = ''): string
    {
        $compValue = $value ? $utility . '-' . $value : $utility;

        return Tw::utility('row', $compValue, $variant);
    }

    // -------------
    // FLEX
    // -------------

    public static function flex(string $value = '', string $variant = ''): string
    {
        return Tw::utility('flex', $value, $variant);
    }

    public static function flexDirection(string $direction, string $value = '', string $variant = ''): string
    {
        $base = $direction === 'row' ? 'flex-row' : 'flex-col';

        return Tw::utility($base, $value, $variant);
    }

    public static function flexWrap(string $value = '', string $variant = ''): string
    {
        $base = $value === 'nowrap' ? 'flex-nowrap' : 'flex-wrap';

        return Tw::utility($base, $value, $variant);
    }

    public static function flexGrow(string $value = '', string $variant = ''): string
    {
        return Tw::utility('flex-grow', $value, $variant);
    }

    public static function flexShrink(string $value = '', string $variant = ''): string
    {
        return Tw::utility('flex-shrink', $value, $variant);
    }

    public static function flexOrder(string $value = '', string $variant = ''): string
    {
        return self::order($value, $variant);
    }

    // -------------
    // ORDER
    // -------------

    public static function order(string $value = '', string $variant = ''): string
    {
        return Tw::utility('order', $value, $variant);
    }

    // -------------
    // EFFECTS
    // -------------

    public static function shadow(string $value = '', string $variant = ''): string
    {
        return Tw::utility('shadow', $value, $variant);
    }

    // -------------
    // TRANSFORM
    // -------------

    public static function transform(string $type = '', string $variant = ''): string
    {
        return Tw::utility('transform', $type, $variant);
    }

    public static function transformOrigin(string $position, string $variant = ''): string
    {
        return Tw::utility('origin', $position, $variant);
    }

    public static function scale(string $value, string $variant = ''): string
    {
        return Tw::utility('scale', $value, $variant);
    }

    public static function rotate(string $value, string $variant = '', bool $isNegative = false): string
    {
        return Tw::utility($isNegative ? '-rotate' : 'rotate', $value, $variant);
    }

    public static function translate(string $value, string $variant = '', bool $isNegative = false): string
    {
        return Tw::utility($isNegative ? '-translate' : 'translate', $value, $variant);
    }

    public static function skew(string $value, string $variant = '', bool $isNegative = false): string
    {
        return Tw::utility($isNegative ? '-skew' : 'skew', $value, $variant);
    }

    // -------------
    // INTERACTIVITY
    // -------------

    public static function cursor(string $value, string $variant = ''): string
    {
        return Tw::utility('cursor', $value, $variant);
    }
}
