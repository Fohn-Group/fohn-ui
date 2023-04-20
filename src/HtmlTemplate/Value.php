<?php

declare(strict_types=1);

namespace Fohn\Ui\HtmlTemplate;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\Service\Ui;

class Value
{
    private string $value = '';

    private function encodeValueToHtml(string $value): string
    {
        return Ui::service()->htmlSpecialChars($value);
    }

    public function set(string $value): self
    {
        if (!preg_match('~~u', $value)) {
            throw new Exception('Value is not valid UTF-8');
        }

        $this->value = $this->encodeValueToHtml($value);

        return $this;
    }

    public function dangerouslySetHtml(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getHtml(): string
    {
        return $this->value;
    }
}
