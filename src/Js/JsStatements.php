<?php

declare(strict_types=1);

namespace Fohn\Ui\Js;

class JsStatements implements JsRenderInterface
{
    /** @var JsRenderInterface[] */
    private array $statements = [];

    final protected function __construct(array $statements = [])
    {
        $this->statements = $statements;
    }

    public static function with(array $statements): self
    {
        $jsArray = new static();
        foreach ($statements as $statement) {
            $jsArray->addStatement($statement);
        }

        return $jsArray;
    }

    public function addStatement(JsRenderInterface $statement): void
    {
        $this->statements[] = $statement;
    }

    public function jsRender(): string
    {
        $render = '';
        foreach ($this->statements as $statement) {
            $render .= $statement->jsRender() . ';';
        }

        return $render;
    }
}
