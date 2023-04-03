<?php

declare(strict_types=1);

namespace Fohn\Ui\Component\Form\Response;

/**
 * Return a form request values response.
 * Use when form component contains form control and need to populate form value
 * via a form vue component request.
 * Response includes an array values that contains controlName => controlValue.
 * Response also include a response status.
 */
class Value
{
    public const SUCCESS = 'success';
    public const ERROR = 'error';

    /** name => value array */
    private array $values = [];
    /** explicitly set status to error will not load value into form. Default is success. */
    private string $status = self::SUCCESS;

    /**
     * @param Value[] $responses
     */
    public static function getAllResponses(array $responses): array
    {
        $values = [];
        foreach ($responses as $response) {
            $values = array_merge($values, $response->getResponse());
        }

        return $values;
    }

    public function mergeValues(array $values): self
    {
        $this->values = array_merge($this->values, $values);

        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getResponse(): array
    {
        return [
            'values' => $this->values,
            'status' => $this->status,
        ];
    }
}
