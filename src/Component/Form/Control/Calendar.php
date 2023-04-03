<?php

declare(strict_types=1);
/**
 * Store \DatetimeInterface value.
 * Use flatpickr for displaying Date selection in form.
 */

namespace Fohn\Ui\Component\Form\Control;

use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsChain;
use Fohn\Ui\Page;

class Calendar extends Input
{
    /** default type (date, time or datetime). */
    protected string $type = 'date';
    /** default format. */
    protected string $format = 'Y-m-d';

    protected string $timezone = 'UTC';

    /** flatpickr configurations. */
    public array $flatPickrConfig = [];

    public string $defaultTemplate = 'vue-component/form/control/calendar.html';

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        $this->initCalendar();
    }

    protected function initCalendar(): void
    {
        $this->flatPickrConfig['dateFormat'] = $this->translateFormat($this->format);
        if ($this->type === 'datetime' || $this->type === 'time') {
            $this->flatPickrConfig['enableTime'] = true;
            $this->flatPickrConfig['time_24hr'] = $this->use24hrTimeFormat($this->format);
            $this->flatPickrConfig['noCalendar'] = ($this->type === 'time');
            $this->flatPickrConfig['enableSeconds'] = $this->useSeconds($this->format);
        }
    }

    /**
     * Return Datetime object as string for Input value.
     *
     * @return mixed|string
     */
    public function getInputValue()
    {
        if ($datetime = parent::getValue()) {
            $datetime->setTimezone(new \DateTimeZone($this->timezone));
        }

        return $datetime ? $datetime->format($this->format) : '';
    }

    /**
     * Set control value from Post request.
     * Set string as Datetime object for control value.
     */
    public function setWithPostValue(?string $value): void
    {
        if ($value !== null) {
            $this->setValue(\DateTime::createFromFormat($this->format, $value, new \DateTimeZone($this->timezone)));
        }
    }

    /**
     * Load flatpickr locale file.
     * Pass it has an option when adding Calendar input.
     *  Form\Control\Calendar::requireLocale($app, 'fr');
     *  $form->getControl('date')->options['locale'] = 'fr';.
     */
    public static function requireLocale(Page $page, string $locale, string $localeUrl): void
    {
        $page->includeJsPackage('flatpickr', $localeUrl);
        // @phpstan-ignore-next-line
        $page->appendJsAction(JsChain::with('flatpickr')->localize(JsChain::with('flatpickr')->l10ns->{$locale}));
    }

    public function translateFormat(string $format): string
    {
        // translate from php to flatpickr.
        $format = preg_replace(['~[aA]~', '~[s]~', '~[g]~'], ['K', 'S', 'G'], $format);

        return $format;
    }

    public function use24hrTimeFormat(string $format): bool
    {
        return !preg_match('~[gGh]~', $format);
    }

    public function useSeconds(string $format): bool
    {
        return (bool) preg_match('~[S]~', $format);
    }

    public function allowMicroSecondsInput(string $format): bool
    {
        return (bool) preg_match('~[u]~', $format);
    }

    protected function beforeHtmlRender(): void
    {
        if ($this->isReadonly()) {
            $this->flatPickrConfig['clickOpens'] = false;
        }

        $this->getTemplate()->trySetJs('flatpickrConfig', Js::object($this->flatPickrConfig));

        parent::beforeHtmlRender();
    }
}
