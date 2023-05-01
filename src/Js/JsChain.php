<?php

declare(strict_types=1);

namespace Fohn\Ui\Js;

use Fohn\Ui\Js\Chain\Chainable;
use Fohn\Ui\Js\Chain\Method;
use Fohn\Ui\Js\Chain\Property;
use Fohn\Ui\Service\Ui;

/**
 * Implements a transparent mapper that will actually translate into JavaScript code.
 *
 * @method store()
 */
class JsChain implements JsRenderInterface
{
    /** Library name ex: jQuery */
    protected string $libraryName;

    /** Optional selector name for library ex: jQuery("mySelector") */
    protected ?JsRenderInterface $selector = null;

    /** @var JsRenderInterface[] Optional arguments for the library. ex: flatpickr(var, fn(){}) */
    protected array $arguments = [];

    /** @var Chainable[] The remaining chaining methods or property. */
    protected array $chains = [];

    final protected function __construct(string $library, JsRenderInterface $variable = null)
    {
        if ($variable) {
            $this->arguments[] = $variable;
        }

        $this->libraryName = $library;
    }

    /**
     * Start of the chain.
     * ex: Js::with('flatpickr', Js::var('')) translate into flatpickr().
     */
    public static function with(string $library, JsRenderInterface $variable = null): self
    {
        return new static($library, $variable);
    }

    public static function withUiLibrary(JsRenderInterface $variable = null): self
    {
        return self::with(Ui::service()->jsLibrary, $variable);
    }

    /**
     * Invoking object instance will create proper method argument.
     * ex: Js::with('flatpickr')(Js::var('myVar'), JsFunction::anonymous()) will translate into flatpickr(myVar, function(){}).
     */
    public function __invoke(JsRenderInterface ...$args): self
    {
        $this->arguments = array_merge($this->arguments, $args);

        return $this;
    }

    /**
     * Calling any method on this object instance will add js method to
     * the chain.
     * $args create with Method are automatically convert to a JsRenderInterface Type using Type::factory()
     * but you may supply your own if needed.
     * For example, when a variable name is needed instead of string.
     *
     * Example below will render myVariable as a regular string.
     * JsChain('myLibrary')->myLibraryFn('myVariable')
     *   => render as myLibrary->myLibraryFn("myVariable");
     *
     * Example below will force render myVariable as a variable name.
     * JsChain('myLibrary')->myLibraryFn(Variable::set('myVariable'))
     *    => render as myLibrary->myLibraryFn(myVariable);
     */
    public function __call(string $name, array $args): self
    {
        $this->chains[] = new Method($name, $args);

        return $this;
    }

    /**
     * Getting any property from this object instance will add
     * a js property to the chain.
     */
    public function __get(string $name): self
    {
        $this->chains[] = new Property($name);

        return $this;
    }

    public function jsRender(): string
    {
        $renderedChain = $this->libraryName;

        if (!empty($this->arguments)) {
            $renderedChain .= Method::renderMethodArguments($this->arguments);
        }

        foreach ($this->chains as $chain) {
            $renderedChain .= '.' . $chain->renderChain();
        }

        return $renderedChain;
    }
}
