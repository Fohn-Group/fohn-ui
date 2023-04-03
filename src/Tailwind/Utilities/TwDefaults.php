<?php

declare(strict_types=1);
/**
 * Generate default class name for utilities set in your themes.
 */

namespace Fohn\Ui\Tailwind\Utilities;

use Fohn\Ui\Tailwind\Tw;

class TwDefaults
{
    public const SEPARATOR = ' / ';

    /**
     * Generate Grid utility class names.
     */
    public static function getGridDefaults(array $screens, array $gapSizes, int $cols = 12, int $rows = 6): string
    {
        $gridModifier = ['span', 'start', 'end'];
        $gridFlow = ['row', 'col', 'row-dense', 'col-dense'];

        $output = '';
        // generating grid-cols
        foreach ($screens as $screen) {
            for ($x = 0; $x <= $cols; ++$x) {
                $output .= Tw::gridType('cols', (string) $x, $screen) . self::SEPARATOR;
            }
            $output .= Tw::gridType('cols', 'none', $screen) . self::SEPARATOR;
        }
        $output .= \PHP_EOL . '-- end grid-cols --' . \PHP_EOL;

        // generating grid-rows
        foreach ($screens as $screen) {
            for ($x = 0; $x <= $rows; ++$x) {
                $output .= Tw::gridType('rows', (string) $x, $screen) . self::SEPARATOR;
            }
            $output .= Tw::gridType('rows', 'none', $screen) . self::SEPARATOR;
        }
        $output .= \PHP_EOL . '-- end grid-rows --' . \PHP_EOL;

        // generating col/ row utility
        foreach ($screens as $screen) {
            for ($x = 0; $x <= $cols + 1; ++$x) {
                foreach ($gridModifier as $modifier) {
                    $output .= Tw::gridCol($modifier, (string) $x, $screen) . self::SEPARATOR;
                    if ($x <= 7) {
                        $output .= Tw::gridRow($modifier, (string) $x, $screen) . self::SEPARATOR;
                    }
                }
            }
        }
        $output .= \PHP_EOL . '-- end col/row --' . \PHP_EOL;

        // generating gap utility
        foreach ($screens as $screen) {
            foreach ($gapSizes as $size) {
                $output .= Tw::gridGap($size, $screen) . self::SEPARATOR;
                $output .= Tw::gridGapAt('x', $size, $screen) . self::SEPARATOR;
                $output .= Tw::gridGapAt('y', $size, $screen) . self::SEPARATOR;
            }
        }
        $output .= \PHP_EOL . '-- end gap --' . \PHP_EOL;

        // generating auto - flow utility
        foreach ($screens as $screen) {
            foreach ($gridFlow as $flow) {
                $output .= Tw::gridAutoFlow($flow, $screen) . self::SEPARATOR;
            }
        }
        $output .= \PHP_EOL . '-- end auto-flow --' . \PHP_EOL;

        return $output;
    }

    public static function getTextDefaults(array $screens, array $textSizes): string
    {
        $textPositions = ['left', 'center', 'right', 'justify'];
        $textVerticalPosition = ['baseline', 'top', 'middle', 'bottom', 'text-top', 'text-bottom', 'sub', 'super'];

        $output = '';
        foreach ($screens as $screen) {
            foreach ($textSizes as $size) {
                $output .= Tw::textSize($size, $screen) . self::SEPARATOR;
            }
        }
        $output .= \PHP_EOL . '-- end text-size --' . \PHP_EOL;
        foreach ($screens as $screen) {
            foreach ($textPositions as $position) {
                $output .= Tw::textAlign($position, $screen) . self::SEPARATOR;
            }
        }
        $output .= \PHP_EOL . '-- end text-align --' . \PHP_EOL;
        foreach ($screens as $screen) {
            foreach ($textVerticalPosition as $position) {
                $output .= Tw::textVerticalAlign($position, $screen) . self::SEPARATOR;
            }
        }
        $output .= \PHP_EOL . '-- end text-vertical-position --' . \PHP_EOL;

        return $output;
    }

    /**
     * Generate minimal border utilities.
     */
    public static function getBorderDefault(array $screens, array $radiusSizes, array $styles): string
    {
        $sizes = ['', '0', '2', '4', '8'];
        $positions = [null, 'top', 'bottom', 'left', 'right', 'top-bottom', 'left-right'];

        $output = '';
        foreach ($screens as $screen) {
            foreach ($sizes as $size) {
                foreach ($positions as $position) {
                    if ($position) {
                        $output .= Tw::borderAt($position, $size, $screen) . self::SEPARATOR;
                    } else {
                        $output .= Tw::border($size, $screen) . self::SEPARATOR;
                    }
                }
            }
        }
        $output .= \PHP_EOL . '-- end border-width --' . \PHP_EOL;
        foreach ($screens as $screen) {
            foreach ($radiusSizes as $size) {
                $output .= Tw::borderRadius($size, $screen) . self::SEPARATOR;
            }
        }
        $output .= \PHP_EOL . '-- end border-radius --' . \PHP_EOL;

        foreach ($screens as $screen) {
            foreach ($styles as $style) {
                $output .= Tw::borderStyle($style, $screen) . self::SEPARATOR;
            }
        }
        $output .= \PHP_EOL . '-- end border-style --' . \PHP_EOL;

        return $output;
    }

    /**
     * Generate minimal color utilities.
     */
    public static function getColorDefault(array $utilities, array $stateVariants, array $colors = [], bool $includeDarkMode = false): string
    {
        $output = '';
        foreach ($utilities as $utility) {
            foreach ($colors as $colorName => $value) {
                foreach ($stateVariants as $state) {
                    $output .= Tw::colour($colorName, $utility, $state) . self::SEPARATOR;
                    if ($includeDarkMode) {
                        $variant = $state ? 'dark:' . $state : 'dark';
                        $output .= Tw::colour($colorName, $utility, $variant) . self::SEPARATOR;
                    }
                }
            }
        }
        $output .= \PHP_EOL . '-- end colors --' . \PHP_EOL;

        return $output;
    }

    public static function getSpacingDefault(array $screens, array $sizes): string
    {
        $positions = [null, 'top', 'bottom', 'left', 'right', 'top-bottom', 'left-right'];
        $output = '';
        foreach ($screens as $screen) {
            foreach ($positions as $position) {
                foreach ($sizes as $size) {
                    if ($position) {
                        $output .= Tw::marginAt($position, $size, $screen) . self::SEPARATOR;
                        $output .= Tw::marginAt($position, $size, $screen, true) . self::SEPARATOR;
                    } else {
                        $output .= Tw::margin($size, $screen) . self::SEPARATOR;
                        $output .= Tw::margin($size, $screen, true) . self::SEPARATOR;
                    }
                }
            }
        }
        $output .= \PHP_EOL . '-- end margin --' . \PHP_EOL;

        foreach ($screens as $screen) {
            foreach ($positions as $position) {
                foreach ($sizes as $size) {
                    if ($position) {
                        $output .= Tw::paddingAt($position, $size, $screen) . self::SEPARATOR;
                        $output .= Tw::paddingAt($position, $size, $screen, true) . self::SEPARATOR;
                    } else {
                        $output .= Tw::padding($size, $screen) . self::SEPARATOR;
                        $output .= Tw::padding($size, $screen, true) . self::SEPARATOR;
                    }
                }
            }
        }
        $output .= \PHP_EOL . '-- end padding --' . \PHP_EOL;

        return $output;
    }

    public static function getWidthDefault(array $screens, array $sizes): string
    {
        $output = '';

        foreach ($screens as $screen) {
            foreach ($sizes as $size) {
                $output .= Tw::width($size, $screen) . self::SEPARATOR;
            }
        }
        $output .= \PHP_EOL . '-- end width --' . \PHP_EOL;

        return $output;
    }

    public static function getHeightDefault(array $screens, array $sizes): string
    {
        $output = '';

        foreach ($screens as $screen) {
            foreach ($sizes as $size) {
                $output .= Tw::height($size, $screen) . self::SEPARATOR;
            }
        }
        $output .= \PHP_EOL . '-- end height --' . \PHP_EOL;

        return $output;
    }
}
