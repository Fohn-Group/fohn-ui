<?php

declare(strict_types=1);

namespace Fohn\Ui\HtmlTemplate;

use Fohn\Ui\HtmlTemplate;

class TagTree
{
    private HtmlTemplate $parentTemplate;
    private string $tag;

    /** @var Value[]|string[]|HtmlTemplate[] */
    private array $children = [];

    final public function __construct(HtmlTemplate $parentTemplate, string $tag)
    {
        $this->parentTemplate = $parentTemplate;
        $this->tag = $tag;
    }

    private function __clone()
    {
    }

    /**
     * @return static
     */
    public function clone(HtmlTemplate $newParentTemplate): self
    {
        $res = new static($newParentTemplate, $this->tag);
        $res->children = [];
        foreach ($this->children as $k => $v) {
            $res->children[$k] = is_string($v) ? $v : clone $v;
        }

        return $res;
    }

    public function getParentTemplate(): HtmlTemplate
    {
        return $this->parentTemplate;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @return Value[]|self[]|HtmlTemplate[]
     */
    public function getChildren(): array
    {
        $res = [];
        $parentTemplate = $this->getParentTemplate();
        foreach ($this->children as $k => $v) {
            $res[$k] = is_string($v) ? $parentTemplate->getTagTree($v) : $v;
        }

        return $res;
    }

    /**
     * @param Value|HtmlTemplate $value
     *
     * @return $this
     */
    public function add(object $value): self
    {
        // very important check
        if ($value instanceof self) {
            throw new \Fohn\Ui\Core\Exception('Tag tree can not be added directly');
        }

        // not strictly needed, but catch issues sooner
        if (!$value instanceof Value && !$value instanceof HtmlTemplate) {
            throw new \Fohn\Ui\Core\Exception('Value must be of type HtmlTemplate\Value or HtmlTemplate');
        }

        $this->children[] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function addTag(string $tag): self
    {
        $this->getParentTemplate()->getTagTree($tag); // check if exists

        $this->children[] = $tag;

        return $this;
    }

    public function deleteChildren(): void
    {
        $this->children = [];
    }

    public function setTopTag(string $tag): void
    {
        $this->tag = $tag;
    }
}
