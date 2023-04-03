<?php

declare(strict_types=1);
/**
 * Created by abelair.
 * Date: 2023-02-08
 * Time: 12:08 p.m.
 */

namespace Fohn\Ui\Tests\Concerns;

use Fohn\Ui\Js\JsRenderInterface;

/**
 * @method setDataId(JsRenderInterface $id)
 * @method deleteRow(string $id)
 * @method updateRow(?string $id, array $row)
 * @method getStoreChain()
 * @method getCellValue(JsRenderInterface $id, JsRenderInterface $name)
 */
class MockJsChain implements \Fohn\Ui\Js\JsRenderInterface
{
    private $libName;

    public function __construct(string $libName)
    {
        $this->libName = $libName;
    }

    public function jsRender(): string
    {
        return $this->libName . ';';
    }
}
