<?php

declare(strict_types=1);

namespace Fohn\Ui\Js;

/**
 * Implements a class that can be mapped into arbitrary JavaScript expression.
 *
 * @method setDataId(JsRenderInterface $id)
 * @method deleteRow(string $id)
 * @method fetchItems(JsRenderInterface $args)
 * @method updateRow(?string $id, array $row)
 * @method getStoreChain()
 * @method getCellValue(JsRenderInterface $id, JsRenderInterface $name)
 * @method clearControlsValue()
 */
interface JsRenderInterface
{
    public function jsRender(): string;
}
