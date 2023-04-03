<?php

declare(strict_types=1);

namespace Fohn\Ui\Js;

use Fohn\Ui\Js\Chain\Method;

/**
 * Implements structure for js closure.
 */
class JsFunction implements JsRenderInterface
{
    /** The name of the function */
    private string $name;
    private bool $isArrow;

    /** @var JsRenderInterface[] Arguments for function */
    protected array $arguments = [];

    /** @var array Array of statements to be executed inside function */
    protected array $statements = [];

    /** Indentation for statements */
    private string $indent = ' ';

    /** Is an immediately invoke function. */
    private bool $isIIIF = false;

    private bool $useDeclaration = false;

    /** IIF Arguments */
    private array $iifArguments = [];

    final protected function __construct(array $args, string $name = '', bool $isArrow = false, bool $useDeclaration = false)
    {
        $this->name = $name;
        $this->isArrow = $isArrow;
        $this->useDeclaration = $useDeclaration;

        foreach ($args as $arg) {
            $this->addArgument($arg);
        }
    }

    public static function arrow(array $args = []): self
    {
        return new static($args, '', true);
    }

    public static function anonymous(array $args = []): self
    {
        return new static($args, '', false);
    }

    public static function named(string $fnName, array $args = []): self
    {
        return new static($args, $fnName);
    }

    /**
     * Declare or call an existing named function.
     * Declare function does not render statements.
     */
    public static function declareFunction(string $fnName, array $args = []): self
    {
        return new static($args, $fnName, false, true);
    }

    public function immediatelyInvokeWith(array $arguments = []): self
    {
        $this->isIIIF = true;
        foreach ($arguments as $argument) {
            $this->addIifArgument($argument);
        }

        return $this;
    }

    private function addIifArgument(JsRenderInterface $argument): void
    {
        $this->iifArguments[] = $argument;
    }

    public function addArgument(JsRenderInterface $argument): self
    {
        $this->arguments[] = $argument;

        return $this;
    }

    /**
     * Add a single statement.
     */
    public function execute(JsRenderInterface $statement): self
    {
        $this->statements[] = $statement;

        return $this;
    }

    /**
     * Add statements to function.
     *
     * @param JsRenderInterface[] $statements
     */
    public function executes(array $statements): self
    {
        foreach ($statements as $statement) {
            $this->execute($statement);
        }

        return $this;
    }

    public function jsRender(): string
    {
        $output = $this->getRenderedFunction();

        if ($this->isIIIF) {
            $output = '(' . $output . ')' . Method::renderMethodArguments($this->iifArguments);
        }

        return $output;
    }

    private function getRenderedFunction(): string
    {
        if ($this->useDeclaration) {
            return $this->renderDeclaration() . ';';
        }
        if ($this->isArrow) {
            return $this->renderArrow();
        }

        return $this->renderFunction();
    }

    private function renderFunction(): string
    {
        $output = 'function ' . $this->name . Method::renderMethodArguments($this->arguments) . ' {' . \PHP_EOL;
        foreach ($this->statements as $statement) {
            $statement = $statement->jsRender();
            $output .= $this->indent . $statement . (!preg_match('~[;}]\s*$~', $statement) ? ';' : '') . \PHP_EOL;
        }

        $output .= '}';

        return $output;
    }

    private function renderDeclaration(): string
    {
        return $this->name . Method::renderMethodArguments($this->arguments);
    }

    private function renderArrow(): string
    {
        $output = Method::renderMethodArguments($this->arguments) . ' => {';
        foreach ($this->statements as $statement) {
            $statement = $statement->jsRender();
            $output .= $this->indent . $statement . (!preg_match('~[;}]\s*$~', $statement) ? ';' : '');
        }

        $output .= '}';

        return $output;
    }
}
