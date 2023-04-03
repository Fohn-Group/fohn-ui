<?php

declare(strict_types=1);
/**
 * Js test.
 */

namespace Fohn\Ui\Tests\Javascript;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\Js;
use Fohn\Ui\Js\JsChain;
use Fohn\Ui\Js\JsFunction;
use Fohn\Ui\Js\Type\ArrayLiteral;
use PHPUnit\Framework\TestCase;

class JsBasicTest extends TestCase
{
    /**
     * Test type rendering.
     */
    public function testJsType(): void
    {
        $string = Js::string('myString');
        $this->assertSame("'myString'", $string->jsRender());

        $var = Js::var('myVar');
        $this->assertSame('myVar', $var->jsRender());

        $bool = Js::boolean(true);
        $this->assertSame('true', $bool->jsRender());

        $floatLit = Js::float(12.4);
        $this->assertSame('12.4', $floatLit->jsRender());

        $integer = Js::integer(22);
        $this->assertSame('22', $integer->jsRender());

        $arrayLit = Js::array(['v1', 'v2']);
        $this->assertSame("['v1','v2']", $arrayLit->jsRender());

        $arrayWithObject = Js::array(['v1', ['v2k1' => 'v2v1']]);
        $this->assertSame("['v1',{v2k1:'v2v1'}]", $arrayWithObject->jsRender());

        $object = Js::object(['k1' => 'v1', 'k2' => 'v2']);
        $this->assertSame("{k1:'v1',k2:'v2'}", $object->jsRender());

        $objectArray = Js::object(['k1' => 'v1', 'k2' => ['a1', 'a2', 'a3']]);
        $this->assertSame("{k1:'v1',k2:['a1','a2','a3']}", $objectArray->jsRender());
    }

    /**
     * Test chain rendering.
     */
    public function testJsChain(): void
    {
        // Test calling chain function.
        $chain = JsChain::with('myLibrary')->doMyLibraryFunction();
        $this->assertSame('myLibrary.doMyLibraryFunction()', $chain->jsRender());

        // Test chaining.
        $chain->callAnotherFunction();
        $this->assertSame('myLibrary.doMyLibraryFunction().callAnotherFunction()', $chain->jsRender());

        // test chain property
        $chainProperty = JsChain::with('myLibrary')->property;
        $this->assertSame('myLibrary.property', $chainProperty->jsRender());

        // test chain function with argument.
        $chainFn = JsChain::with('myLibrary')->myLibraryFn(Js::var('arg1'), Js::boolean(false));
        $this->assertSame('myLibrary.myLibraryFn(arg1,false)', $chainFn->jsRender());

        // passing js chain property as argument.
        $chainMix = JsChain::with('myLibrary')->myLibraryFn(JsChain::with('myLibrary')->property);
        $this->assertSame('myLibrary.myLibraryFn(myLibrary.property)', $chainMix->jsRender());

        // passing array as argument.
        $chainArray = JsChain::with('myLibrary')->myLibraryFn(ArrayLiteral::set(['v1', 'v2']));
        $this->assertSame("myLibrary.myLibraryFn(['v1','v2'])", $chainArray->jsRender());

        // passing function as argument.
        $chainFn = JsChain::with('myLibrary')->myLibraryFn(JsFunction::arrow([Js::var('fnArg')])->execute(Js::from('console.log(fnArg)')));
        $expected = 'myLibrary.myLibraryFn((fnArg) => { console.log(fnArg);})';
        $this->assertSame($expected, $chainFn->jsRender());
    }

    /**
     * Test function rendering.
     */
    public function testJsFunction(): void
    {
        // render a named function.
        $nameFn = JsFunction::named('myFunc', [Js::var('var')])->execute(Jquery::withThis()->text(Js::var('var')));
        $expected = 'function myFunc(var) { jQuery(this).text(var);}';
        $this->assertSame($expected, preg_replace('~\n*~', '', $nameFn->jsRender()));

        // declare function does not render statements.
        $declareFn = JsFunction::declareFunction('myFunc', [Js::string('arg')])->execute(Jquery::withThis()->text(Js::var('var')));
        $this->assertSame("myFunc('arg');", $declareFn->jsRender());

        // render an arrow function.
        $arrowFn = JsFunction::arrow([Js::var('var')])->execute(Jquery::withThis()->text(Js::var('var')));
        $expected = '(var) => { jQuery(this).text(var);}';
        $this->assertSame($expected, preg_replace('~\n*~', '', $arrowFn->jsRender()));

        $arrowFn->immediatelyInvokeWith([Js::integer(22)]);
        $expected = '((var) => { jQuery(this).text(var);})(22)';
        $this->assertSame($expected, preg_replace('~\n*~', '', $arrowFn->jsRender()));

        $anonymous = JsFunction::anonymous([Js::var('var')])->execute(Jquery::withThis()->text(Js::var('var')));
        $expected = 'function (var) { jQuery(this).text(var);}';
        $this->assertSame($expected, preg_replace('~\n*~', '', $anonymous->jsRender()));

        $anonymous->immediatelyInvokeWith([Js::string('arg')]);
        $expected = "(function (var) { jQuery(this).text(var);})('arg')";
        $this->assertSame($expected, preg_replace('~\n*~', '', $anonymous->jsRender()));
    }

    public function testTemplateTag(): void
    {
        $js = Js::from('const test = ({{var}}, {{options}})=>{}', ['var' => Js::var('varName'), 'options' => Js::object(['k' => 'v'])]);
        $expected = "const test = (varName, {k:'v'})=>{}";
        $this->assertSame($expected, $js->jsRender());
    }

    public function testBadTemplateTag(): void
    {
        $this->expectException(Exception::class);
        $js = Js::from('const test = ({{v}})=>{}', ['vars' => Js::var('varName')]);
        $js->jsRender();
    }
}
