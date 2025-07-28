<?php

declare(strict_types=1);
/**
 * Utility for Vue Components.
 */

namespace Fohn\Ui\Component;

use Fohn\Ui\Js\JsChain;
use Fohn\Ui\Page;
use Fohn\Ui\Page\Package;

class Utils
{
    /**
     * Load flatpickr locale file.
     * Pass it has an option when adding Calendar input.
     *  Utils::requireLocale($app, 'fr');
     *  $form->getControl('date')->options['locale'] = 'fr';.
     */
    public static function requireFLatPickrLocale(Page $page, string $locale): void
    {
        $localeUrl = "https://npmcdn.com/flatpickr/dist/l10n/{$locale}.js";
        $page->includePackage('flatpickr', Package::addScript($localeUrl));
        // @phpstan-ignore-next-line
        $page->appendJsAction(JsChain::with('flatpickr')->localize(JsChain::with('flatpickr')->l10ns->{$locale}));
    }

    /**
     * Return proper flat-pickr configuration based on date, time or datetime type
     * and display format.
     */
    public static function getFlatPickrConfig(string $dataType, string $format): array
    {
        $flatPickrConfig['dateFormat'] = preg_replace(['~[aA]~', '~[s]~', '~[g]~'], ['K', 'S', 'G'], $format);
        if ($dataType === 'datetime' || $dataType === 'time') {
            $flatPickrConfig['enableTime'] = true;
            $flatPickrConfig['time_24hr'] = !preg_match('~[gGh]~', $format);
            $flatPickrConfig['noCalendar'] = ($dataType === 'time');
            $flatPickrConfig['enableSeconds'] = (bool) preg_match('~[S]~', $format);
        }

        return $flatPickrConfig;
    }
}
