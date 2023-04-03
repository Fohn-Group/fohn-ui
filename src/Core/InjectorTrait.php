<?php

declare(strict_types=1);

/**
 * Inject properties to Object Instance.
 */

namespace Fohn\Ui\Core;

trait InjectorTrait
{
    protected function injectDefaults(array $properties): self
    {
        foreach ($properties as $key => $val) {
            if (property_exists($this, $key)) {
                $this->{$key} = $val;
            } else {
                $this->setMissingProperty($key, $val);
            }
        }

        return $this;
    }

    /**
     * @param mixed $value
     */
    protected function setMissingProperty(string $propertyName, $value): void
    {
        throw (new Exception('Property for specified object is not defined'))
            ->addMoreInfo('object', $this)
            ->addMoreInfo('property', $propertyName)
            ->addMoreInfo('value', $value);
    }
}
