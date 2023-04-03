<?php

declare(strict_types=1);
/**
 * Created by abelair.
 * Date: 2023-02-13
 * Time: 10:28 a.m.
 */

namespace Fohn\Ui\Tests\Concerns;

use Fohn\Ui\Core\InjectorTrait;

class MockAbstractView
{
    use InjectorTrait;
}

class MockView extends MockAbstractView
{
    public string $publicProps = 'aProps';
    protected string $protectedProps = 'bProps';
    private string $privateProps;

    public function __construct(array $defaults = [])
    {
        $this->injectDefaults($defaults);
    }

    public function getProtectedProps(): string
    {
        return $this->protectedProps;
    }
}
