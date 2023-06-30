<?php

declare(strict_types=1);

/**
 * Theme Interface.
 */

namespace Fohn\Ui\Service\Theme;

use Fohn\Ui\View;

interface ThemeInterface
{
    public function init(): void;

    public static function colorAs(string $color, View $view, string $type): void;

    public static function styleAs(string $component, array $args): void;

    public function getSupportedVariants(): array;

    public function getColours(): array;
}
