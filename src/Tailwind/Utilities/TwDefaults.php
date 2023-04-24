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

    public static function getCursorDefault(array $screens): string
    {
        $cursorTypes = ['auto', 'default', 'pointer', 'wait', 'text', 'move', 'help', 'not-allowed', 'none', 'context-menu',
            'progress', 'cell', 'crosshair', 'vertical-text', 'alias', 'copy', 'no-drop', 'grab', 'grabbing', 'all-scroll',
            'col-resize', 'row-resize', 'n-resize', 'e-resize', 's-resize', 'w-resize', 'ne-resize', 'nw-resize', 'sw-resize', 'ew-resize',
            'ns-resize', 'nesw-resize', 'nwse-resize', 'zoom-in', 'zoom-out', ];

        $output = '';

        foreach ($cursorTypes as $type) {
            foreach ($screens as $screen) {
                $output .= Tw::utility('cursor', $type, $screen) . self::SEPARATOR;
            }
        }
        $output .= \PHP_EOL . '-- end cursor --' . \PHP_EOL;

        return $output;
    }

    public static function getFontDefault(array $screens): string
    {
        $allFonts = [
            'family' => ['font-sans', 'font-serif', 'font-mono'],
            'smoothing' => ['antialiased', 'subpixel-antialiased'],
            'style' => ['italic', 'non-italic'],
            'weight' => ['font-thin', 'font-extralight', 'font-light', 'font-normal', 'font-medium', 'font-semibold', 'font-bold', 'font-extrabold', 'font-black'],
            'variant_numeric' => ['normal-nums', 'ordinal', 'slashed-zero', 'lining-nums', 'oldstyle-nums', 'proportional-nums', 'tabular-nums', 'diagonal-fractions', 'stacked-fractions'],
            'spacing' => ['tracking-tighter', 'tracking-light', 'tracking-normal', 'tracking-wide', 'tracking-widest'],
            'clamp' => ['line-clamp-1', 'line-clamp-2', 'line-clamp-3', 'line-clamp-4', 'line-clamp-5', 'line-clamp-6', 'line-clamp-none'],
            'height' => ['leading-3', 'leading-4', 'leading-5', 'leading-6', 'leading-7',
                'leading-8', 'leading-9', 'leading-10', 'leading-none', 'leading-tight', 'leading-snug', 'leading-normal',
                'leading-relaxed', 'leading-loose'],
            'transform' => ['uppercase', 'lowercase', 'capitalize', 'normal-case'],
            'overflow' => ['truncate', 'text-ellipsis', 'text-clip'],
            'whitespace' => ['whitespace-normal', 'whitespace-nowrap', 'whitespace-pre', 'whitespace-pre-line', 'whitespace-pre-wrap', 'whitespace-break-spaces'],
            'word_break' => ['break-normal', 'break-words', 'break-all', 'break-keep'],
            'hyphens' => ['hyphens-none', 'hyphens-manual', 'hyphens-auto'],
        ];

        $output = '';
        foreach ($allFonts as $utility => $bases) {
            foreach ($bases as $base) {
                foreach ($screens as $screen) {
                    $output .= Tw::utility($base, '', $screen) . self::SEPARATOR;
                }
            }
            $output .= \PHP_EOL . '-- end font ' . $utility . ' --' . \PHP_EOL;
        }

        return $output;
    }

    public static function getFlexDefault(array $screens, array $sizes): string
    {
        $allFlexs = [
            'direction' => ['flex-row', 'flex-row-reverse', 'flex-col', 'flex-col-reverse'],
            'wrap' => ['flex-wrap', 'flex-wrap-reverse', 'flex-nowrap'],
            'flex' => ['flex-1', 'flex-auto', 'flex-initial', 'flex-none'],
            'grow' => ['grow', 'grow-0'],
            'shrink' => ['shrink', 'shrink-0'],
        ];

        $orderSizes = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', 'first', 'last', 'none'];

        $output = '';
        // Basis
        foreach ($screens as $screen) {
            foreach ($sizes as $size) {
                $output .= Tw::utility('basis', $size, $screen) . self::SEPARATOR;
            }
            $output .= \PHP_EOL;
        }

        $output .= '-- end flex basis --' . \PHP_EOL;

        foreach ($allFlexs as $utility => $bases) {
            foreach ($bases as $base) {
                foreach ($screens as $screen) {
                    $output .= Tw::utility($base, '', $screen) . self::SEPARATOR;
                }
            }
            $output .= \PHP_EOL . '-- end flex ' . $utility . ' --' . \PHP_EOL;
        }

        foreach ($orderSizes as $size) {
            foreach ($screens as $screen) {
                $output .= Tw::utility('order', $size, $screen) . self::SEPARATOR;
            }
            $output .= \PHP_EOL;
        }

        $output .= '-- end order --' . \PHP_EOL;

        return $output;
    }

    public static function getJustifyDefault(array $screens): string
    {
        $utilities = ['', 'items', 'self'];
        $positions = ['auto', 'normal', 'start', 'end', 'center', 'between', 'around', 'evenly', 'stretch'];

        $output = '';

        foreach ($utilities as $utility) {
            foreach ($screens as $screen) {
                foreach ($positions as $position) {
                    $base = $utility ? 'justify-' . $utility : 'justify';
                    $output .= Tw::utility($base, $position, $screen) . self::SEPARATOR;
                }
                $output .= \PHP_EOL;
            }
            $output .= \PHP_EOL;
        }

        $output .= \PHP_EOL . '-- end justify --' . \PHP_EOL;

        return $output;
    }

    public static function getAlignDefault(array $screens): string
    {
        return self::getAlignPlaceDefault($screens);
    }

    public static function getPlaceDefault(array $screens): string
    {
        return self::getAlignPlaceDefault($screens, true);
    }

    private static function getAlignPlaceDefault(array $screens, bool $isPlace = false): string
    {
        $alignUtilities = ['content', 'items', 'self'];
        $positions = ['auto', 'normal', 'start', 'end', 'center', 'between', 'stretch', 'around', 'evenly', 'baseline'];

        $output = '';
        foreach ($alignUtilities as $utility) {
            foreach ($screens as $screen) {
                foreach ($positions as $position) {
                    $base = $isPlace ? 'place-' . $utility : $utility;
                    $output .= Tw::utility($base, $position, $screen) . self::SEPARATOR;
                }
            }
            $output .= \PHP_EOL;
        }
        $endOutput = $isPlace ? '-- end place --' : '-- end align --';
        $output .= \PHP_EOL . $endOutput . \PHP_EOL;

        return $output;
    }
}
