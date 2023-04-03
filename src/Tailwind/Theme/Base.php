<?php

declare(strict_types=1);

/**
 * Base theme configuration.
 */

namespace Fohn\Ui\Tailwind\Theme;

use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\Tailwind\Utilities\TwDefaults;
use Fohn\Ui\View;

class Base implements ThemeInterface
{
    public const PAGE = 'page';
    public const BUTTON = 'btn';
    public const MESSAGE = 'message';
    public const TAG = 'tag';
    public const CONSOLE = 'console';
    public const CHIP = 'chip';
    public const LINK = 'link';

    /** @var string[] Theme colors */
    public array $colors = TwConstant::COLORS;

    /** @var string[] Default theme screen size. */
    public array $screens = TwConstant::SCREEN_VARIANTS;

    /** @var string[] Default theme state variants. */
    public array $states = TwConstant::STATE_VARIANTS;

    /** @var string[] Default theme space values. */
    public array $spaces = TwConstant::SPACE_VALUES;

    /** @var string[] Default theme width values. */
    public array $widths = TwConstant::WIDTH_VALUES;

    /** @var string[] Default theme height values. */
    public array $heights = TwConstant::HEIGHT_VALUES;

    /** @var string[] Default theme color Tw utilities. */
    public array $colorUtilities = TwConstant::COLOR_UTILITIES;

    /** @var string[] Default theme text size. */
    public array $textSizes = TwConstant::TEXT_SIZE;

    /** @var string[] Default theme gap size. */
    public array $gapSizes = TwConstant::GRID_GAP_SIZE;

    /** @var string[] Default theme border radius. */
    public array $borderRadius = TwConstant::BORDER_RADIUS_SIZE;

    /** @var string[] Defautl theme border style. */
    public array $borderStyles = TwConstant::BORDER_STYLES;

    public array $supportedVariants = [];

    /** Tw utilities that must be included in css file. */
    public array $safeLists = [];

    public bool $hasDarkMode = false;

    /** @var static|null */
    protected static $instance;

    final protected function __construct()
    {
        // singleton
    }

    /**
     * Override this method for your own theme initialization.
     */
    public function init(): void
    {
    }

    public function getSupportedVariants(): array
    {
        return $this->supportedVariants;
    }

    public function getColours(): array
    {
        return $this->colors;
    }

    public static function colorAs(string $color, View $view, string $type): void
    {
    }

    public static function styleAs(string $component, array $args): void
    {
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
            static::$instance->init();
        }

        return static::$instance;
    }

    /**
     * Generated Tw utilities need to supported your theme for which you can safely use Tw::utility() method.
     * Make sure output create with this method will be capture by your Tailwind configuration (tailwind.config.js).
     *
     * Ex: file_put_contents('fohn-theme-tw.txt', Fohn::getThemeCss());
     * All utilities needed to support this theme will be added to fohn-theme-tw.txt file.
     * Your Tailwind configuration content should then include the text file for proper css generation.
     */
    public static function getThemeCss(): string
    {
        $theme = self::getInstance();
        $output = $theme->generatedSafeList();
        $output .= TwDefaults::getColorDefault($theme->colorUtilities, $theme->states, $theme->colors, $theme->hasDarkMode);
        $output .= TwDefaults::getGridDefaults($theme->screens, $theme->gapSizes);
        $output .= TwDefaults::getTextDefaults($theme->screens, $theme->textSizes);
        $output .= TwDefaults::getBorderDefault($theme->screens, $theme->borderRadius, $theme->borderStyles);
        $output .= TwDefaults::getSpacingDefault($theme->screens, $theme->spaces);
        $output .= TwDefaults::getWidthDefault($theme->screens, $theme->widths);
        $output .= TwDefaults::getHeightDefault($theme->screens, $theme->heights);

        return $output;
    }

    /**
     * Generate Tw utilities based on your safeLists values.
     * $safeList array should be set as:
     *  [ $utilityName => ['value' => $values, 'variant' => $variants] ]
     * $utilityName = The name of Tw utility that you need to generated;
     * $values = Each value need for that utility;
     * $variants = Each variant need for that utility;.
     */
    public function generatedSafeList(): string
    {
        $output = '';

        foreach ($this->safeLists as $utility => $var) {
            foreach ($var['variant'] as $variant) {
                foreach ($var['value'] as $value) {
                    $output .= Tw::utility($utility, $value, $variant) . TwDefaults::SEPARATOR;
                }
            }
        }
        $output .= \PHP_EOL . '-------- end safelist ----------' . \PHP_EOL;

        return $output;
    }
}
