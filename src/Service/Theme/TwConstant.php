<?php

declare(strict_types=1);
/**
 * Theme Utility safe value.
 *
 * These are values that you can safely use with Utilities function, within the framework, and be sure
 * they will be part of fohn-ui.css.
 *
 * Use TwDefaults class in order to create defaults utilities need for your projects where you can override
 * these constants.
 */

namespace Fohn\Ui\Service\Theme;

class TwConstant
{
    /** @var string[] Color mapping */
    public const COLORS = [
        'primary' => 'purple-700',
        'primary-light' => 'purple-600',
        'primary-dark' => 'purple-900',
        'secondary' => 'pink-500',
        'secondary-light' => 'pink-400',
        'secondary-dark' => 'pink-700',
        'accent' => 'green-400',
        'accent-light' => 'green-300',
        'accent-dark' => 'green-500',
        'black' => 'gray-700',
        'white' => 'white',
        'info' => 'blue-500',
        'info-light' => 'blue-400',
        'info-dark' => 'blue-700',
        'success' => 'green-600',
        'success-light' => 'green-500',
        'success-dark' => 'green-800',
        'error' => 'red-600',
        'error-light' => 'red-500',
        'error-dark' => 'red-700',
        'warning' => 'yellow-500',
        'warning-light' => 'yellow-400',
        'warning-dark' => 'yellow-700',
        'transparent' => 'transparent',
        'neutral' => 'gray-300',
        'neutral-light' => 'gray-200',
        'neutral-dark' => 'gray-400',
    ];

    public const TEXT_SIZE = [
        'xs', 'sm', 'base', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl', '8xl', '9xl',
    ];

    public const SCREEN_VARIANTS = [
        '', 'sm', 'md', 'lg', 'xl', '2xl',
    ];

    public const SPACE_VALUES = [
        'auto', '0', 'px', '0.5', '1', '1.5', '2', '2.5', '3', '3.5', '4', '5', '6', '7', '8', '9', '10', '12', '14', '16', '20',
    ];

    public const WIDTH_VALUES = [
        '0', 'px', '0.5', '1', '1.5', '2', '2.5', '3', '3.5', '4', '5', '6', '7', '8', '9', '10', '11', '12', '14', '16',
        '20', '24', '28', '32', '36', '40', '44', '48', '52', '60', '64', '72', '80', '96',
        'auto', '1/2', '1/3', '2/3', '1/4', '2/4', '3/4', '1/5', '2/5', '3/5', '4/5', '1/6', '2/6', '3/6', '4/6', '5/6',
        '1/12', '2/12', '3/12', '4/12', '5/12', '6/12', '7/12', '8/12', '9/12', '10/12', '11/12', 'full', 'screen', 'min',
        'max', 'fit',
    ];

    public const HEIGHT_VALUES = [
        '0', 'px', '0.5', '1', '1.5', '2', '2.5', '3', '3.5', '4', '5', '6', '7', '8', '9', '10', '11', '12', '14', '16',
        '20', '24', '28', '32', '36', '40', '44', '48', '52', '60', '64', '72', '80', '96',
        'auto', '1/2', '1/3', '2/3', '1/4', '2/4', '3/4', '1/5', '2/5', '3/5', '4/5', '1/6', '2/6', '3/6', '4/6', '5/6',
        'full', 'screen', 'min', 'max', 'fit',
    ];

    public const COLOR_UTILITIES = [
        'bg',
        'border',
        'focus',
        'text',
        'decoration',
        'shadow',
        'accent',
        'caret',
        'fill',
        'stroke',
        'ring',
        'ring-offset',
        'outline',
        'divide',
        'from',
        'via',
    ];

    public const STATE_VARIANTS = [
        '',
        'hover',
        'active',
        'focus',
        'disabled',
    ];

    public const GRID_GAP_SIZE = ['0', 'px', '0.5', '1', '1.5', '2', '3', '4', '5', '6'];
    public const BORDER_RADIUS_SIZE = ['', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', 'full', 'none', 'full'];
    public const BORDER_STYLES = ['solid', 'dashed', 'dotted', 'double', 'hidden', 'none'];
}
