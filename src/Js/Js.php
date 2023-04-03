<?php

declare(strict_types=1);

namespace Fohn\Ui\Js;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\Js\Type\ArrayLiteral;
use Fohn\Ui\Js\Type\Boolean;
use Fohn\Ui\Js\Type\FloatLiteral;
use Fohn\Ui\Js\Type\Integer;
use Fohn\Ui\Js\Type\ObjectLiteral;
use Fohn\Ui\Js\Type\StringLiteral;
use Fohn\Ui\Js\Type\Type;
use Fohn\Ui\Js\Type\Variable;

/**
 * Implements a class that can be mapped into arbitrary JavaScript.
 */
class Js implements JsRenderInterface
{
    private string $template;
    private array $tags = [];

    final private function __construct(string $template, array $tags)
    {
        $this->template = $template;
        foreach ($tags as $key => $tag) {
            $this->tags[$key] = Type::factory($tag);
        }
    }

    /**
     * Create Js statement/expression using a template.
     * Template tag {{}} are replaced by their corresponding tag value during render.
     *
     * ex: Js::from('const c = {{a}} + {{b}}', ['a' => 2, 'b' => 2])->jsRender()
     * will result in 'const c = 2+2;'
     */
    public static function from(string $template, array $tags = []): self
    {
        return new static($template, $tags);
    }

    /**
     * Output javascript variable string. Js::var('myVar') => myVar;.
     */
    public static function var(string $variableName): JsRenderInterface
    {
        return Variable::set($variableName);
    }

    /**
     * Output javascript string. Js::string('myString') => 'myString';.
     */
    public static function string(string $stringValue): JsRenderInterface
    {
        return StringLiteral::set($stringValue);
    }

    /**
     * Output javascript integer. Js::integer(12) => 12;.
     */
    public static function integer(int $intValue): JsRenderInterface
    {
        return Integer::set($intValue);
    }

    public static function float(float $floatValue): JsRenderInterface
    {
        return FloatLiteral::set($floatValue);
    }

    public static function boolean(bool $value): JsRenderInterface
    {
        return Boolean::set($value);
    }

    public static function array(array $value): JsRenderInterface
    {
        return ArrayLiteral::set($value);
    }

    public static function object(array $value): JsRenderInterface
    {
        return ObjectLiteral::set($value);
    }

    public function jsRender(): string
    {
        $result = preg_replace_callback(
            '~{{([\w\-:]+)}}~',
            function ($matches) {
                $tag = $matches[1];

                if (!isset($this->tags[$tag])) {
                    throw (new Exception('Tag is not defined in Js template'))
                        ->addMoreInfo('tag', $tag)
                        ->addMoreInfo('template', $this->template);
                }

                return $this->tags[$tag]->jsRender();
            },
            $this->template
        );

        return trim(preg_replace('~(;;)~', ';', $result));
    }
}
