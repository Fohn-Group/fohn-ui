<?php

declare(strict_types=1);
/**
 * Store \DatetimeInterface value.
 * Use flatpickr for displaying Date selection in form.
 */

namespace Fohn\Ui\Component\Form\Control;

use Fohn\Ui\Component\Utils;
use Fohn\Ui\Js\Js;

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
        $this->flatPickrConfig = Utils::getFlatPickrConfig($this->type, $this->format);
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

    protected function beforeHtmlRender(): void
    {
        if ($this->isReadonly()) {
            $this->flatPickrConfig['clickOpens'] = false;
        }

        $this->getTemplate()->trySetJs('flatpickrConfig', Js::object($this->flatPickrConfig));

        parent::beforeHtmlRender();
    }
}
