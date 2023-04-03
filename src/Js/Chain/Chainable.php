<?php

declare(strict_types=1);

/**
 * Created by abelair.
 * Date: 2021-04-28
 * Time: 1:11 p.m.
 */

namespace Fohn\Ui\Js\Chain;

interface Chainable
{
    public function renderChain(): string;
}
