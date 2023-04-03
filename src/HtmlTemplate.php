<?php

declare(strict_types=1);

namespace Fohn\Ui;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\HtmlTemplate\TagTree;
use Fohn\Ui\HtmlTemplate\Value as HtmlValue;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Service\Ui;

class HtmlTemplate
{
    public const TOP_TAG = '_top';
    public const BEFORE_TEMPLATE_TAG = '__beforeTemplate';
    public const AFTER_TEMPLATE_TAG = '__afterTemplate';
    public const MAIN_TEMPLATE_TAG = 'Content';
    private bool $hasBeforeAfterTag;

    /** @var array<string, string|false> */
    private static array $filesCache = [];

    /** @var array<string, TagTree[]> */
    private static array $parseCache = [];

    /** @var TagTree[] */
    private array $tagTrees = [];
    private string $source = '';

    final public function __construct(string $template = '', bool $addBeforeAfter = true)
    {
        $this->hasBeforeAfterTag = $addBeforeAfter;
        $this->loadFromString($template);
    }

    public function getRegionTagName(string $suffix): array
    {
        $regions = [];
        foreach ($this->tagTrees as $tagName => $tagTree) {
            if (strpos($tagName, $suffix)) {
                $regions[] = $tagName;
            }
        }

        return $regions;
    }

    public function hasTag(string $tag): bool
    {
        return isset($this->tagTrees[$tag]);
    }

    public function getTagTree(string $tag): TagTree
    {
        $this->assertHasTag($tag);

        return $this->tagTrees[$tag];
    }

    public function __clone()
    {
        $this->tagTrees = $this->cloneTagTrees($this->tagTrees);
    }

    public function cloneRegion(string $tag): self
    {
        $template = new static();
        $template->tagTrees = $template->cloneTagTrees($this->tagTrees);

        // rename top tag tree
        $topTagTree = $template->tagTrees[$tag];
        unset($template->tagTrees[$tag]);
        $template->tagTrees[self::TOP_TAG] = $topTagTree;
        $topTagTree->setTopTag(self::TOP_TAG);

        return $template;
    }

    /**
     * This function will replace region referred by $tag to a new content.
     *
     * If tag is found inside template several times, all occurrences are
     * replaced.
     */
    public function set(string $tag, string $value): self
    {
        $this->setOrAppend($tag, $value, true, false);

        return $this;
    }

    /**
     * Same as set(), but won't generate exception for non-existing
     * $tag.
     */
    public function trySet(string $tag, string $value): self
    {
        $this->setOrAppend($tag, $value, true, false, false);

        return $this;
    }

    public function trySetMany(array $tags): self
    {
        foreach ($tags as $tag => $value) {
            $this->trySet($tag, $value);
        }

        return $this;
    }

    public function setJs(string $tag, JsRenderInterface $value): self
    {
        $this->setOrAppend($tag, $value->jsRender(), false, false);

        return $this;
    }

    public function trySetJs(string $tag, JsRenderInterface $value): self
    {
        $this->setOrAppend($tag, $value->jsRender(), false, false, false);

        return $this;
    }

    /**
     * Set value of a tag to a HTML content. The value is set without
     * encoding, so you must be sure to sanitize.
     */
    public function dangerouslySetHtml(string $tag, string $value): self
    {
        $this->setOrAppend($tag, $value, false, false);

        return $this;
    }

    /**
     * See dangerouslySetHtml() but won't generate exception for non-existing
     * $tag.
     */
    public function tryDangerouslySetHtml(string $tag, string $value): self
    {
        $this->setOrAppend($tag, $value, false, false, false);

        return $this;
    }

    /**
     * Add more content inside a tag.
     */
    public function append(string $tag, string $value): self
    {
        $this->setOrAppend($tag, $value, true, true);

        return $this;
    }

    /**
     * Same as append(), but won't generate exception for non-existing
     * $tag.
     */
    public function tryAppend(string $tag, string $value): self
    {
        $this->setOrAppend($tag, $value, true, true, false);

        return $this;
    }

    /**
     * Add more content inside a tag. The content is appended without
     * encoding, so you must be sure to sanitize.
     */
    public function dangerouslyAppendHtml(string $tag, string $value): self
    {
        $this->setOrAppend($tag, $value, false, true);

        return $this;
    }

    /**
     * Same as dangerouslyAppendHtml(), but won't generate exception for non-existing
     * $tag.
     */
    public function tryDangerouslyAppendHtml(string $tag, string $value): self
    {
        $this->setOrAppend($tag, $value, false, true, false);

        return $this;
    }

    /**
     * Empty contents of specified region. If region contains sub-hierarchy,
     * it will be also removed.
     */
    public function del(string $tag): self
    {
        $tagTree = $this->getTagTree($tag);
        $tagTree->deleteChildren();

        return $this;
    }

    /**
     * Similar to del() but won't throw exception if tag is not present.
     */
    public function tryDel(string $tag): self
    {
        if ($this->hasTag($tag)) {
            $this->del($tag);
        }

        return $this;
    }

    public function loadFromFile(string $filename): self
    {
        if ($this->tryLoadFromFile($filename) !== null) {
            return $this;
        }

        throw (new Exception('Unable to read template from file'))
            ->addMoreInfo('filename', $filename);
    }

    /**
     * Same as load(), but will not throw an exception.
     */
    public function tryLoadFromFile(string $filename): ?self
    {
        if ($filename = realpath($filename)) {
            $this->source = $filename;
            if (!isset(self::$filesCache[$filename])) {
                /** @var string|false $data */
                $data = file_get_contents($filename);
                if ($data !== false) {
                    $data = preg_replace('~(?:\r\n?|\n)$~s', '', $data); // always trim end NL
                }
                self::$filesCache[$filename] = $data;
            }

            $this->loadFromString(self::$filesCache[$filename]);

            return $this;
        }

        return null;
    }

    public function loadFromString(string $str): self
    {
        $this->parseTemplate($str);

        return $this;
    }

    protected function unsetFromTagTree(TagTree $tagTree, int $k): void
    {
        \Closure::bind(function () use ($tagTree, $k) {
            unset($tagTree->children[$k]);
        }, null, TagTree::class)();
    }

    protected function emptyTagTree(TagTree $tagTree): void
    {
        foreach ($tagTree->getChildren() as $k => $v) {
            if ($v instanceof TagTree) {
                $this->emptyTagTree($v);
            } else {
                $this->unsetFromTagTree($tagTree, $k);
            }
        }
    }

    /**
     * Internal method for setting or appending content in $tag.
     *
     * If tag contains another tag trees, these tag trees are emptied.
     */
    protected function setOrAppend(string $tag, string $value, bool $encodeHtml = true, bool $append = false, bool $throwIfNotFound = true): void
    {
        if (!$throwIfNotFound && !$this->hasTag($tag)) {
            return;
        }

        $htmlValue = new HtmlValue();
        if ($encodeHtml) {
            $htmlValue->set($value);
        } else {
            $htmlValue->dangerouslySetHtml($value);
        }

        // set or append value
        $tagTree = $this->getTagTree($tag);
        if (!$append) {
            $this->emptyTagTree($tagTree);
        }
        $tagTree->add($htmlValue);
    }

    protected function parseTemplate(string $str): void
    {
        $str = $this->hasBeforeAfterTag
            ? $this->wrapTagName(self::BEFORE_TEMPLATE_TAG) . $str . $this->wrapTagName(self::AFTER_TEMPLATE_TAG)
            : $str;
        $cKey = Ui::service()->factoryId($str, '', 20);
        if (!isset(self::$parseCache[$cKey])) {
            // expand self-closing tags {$tag} -> {tag}{/tag}
            $str = preg_replace('~{\$([\w\-:]+)}~', '{\1}{/\1}', $str);

            // capture only tag {tag}{/tag}. Do not capture @{value} or {{value}}.
            $input = preg_split('~(?<!@|{){(/?[\w\-:]*)}~', $str, -1, \PREG_SPLIT_DELIM_CAPTURE);
            $inputReversed = array_reverse($input); // reverse to allow to use fast array_pop()

            $this->tagTrees[self::TOP_TAG] = $this->parseTemplateTree($inputReversed);

            self::$parseCache[$cKey] = $this->tagTrees;
        } else {
            $this->tagTrees = $this->cloneTagTrees(self::$parseCache[$cKey]);
        }
    }

    protected function parseTemplateTree(array &$inputReversed, string $openedTag = null): TagTree
    {
        $tagTree = new TagTree($this, $openedTag ?? self::TOP_TAG);

        $chunk = array_pop($inputReversed);
        if ($chunk !== '') {
            $tagTree->add((new HtmlValue())->dangerouslySetHtml($chunk));
        }

        while (($tag = array_pop($inputReversed)) !== null) {
            $firstChar = substr($tag, 0, 1);
            if ($firstChar === '/') { // is closing tag
                $tag = substr($tag, 1);
                if ($openedTag === null
                    || ($tag !== '' && $tag !== $openedTag)) {
                    throw (new Exception('Template parse error: tag was not opened'))
                        ->addMoreInfo('opened_tag', $openedTag)
                        ->addMoreInfo('tag', $tag);
                }

                $openedTag = null;

                break;
            }

            // is new/opening tag
            $childTagTree = $this->parseTemplateTree($inputReversed, $tag);
            $this->tagTrees[$tag] = $childTagTree;
            $tagTree->addTag($tag);

            $chunk = array_pop($inputReversed);
            if ($chunk !== null && $chunk !== '') {
                $tagTree->add((new HtmlValue())->dangerouslySetHtml($this->parseSpecialCharacter($chunk)));
            }
        }

        if ($openedTag !== null) {
            throw (new Exception('Template parse error: tag is not closed'))
                ->addMoreInfo('tag', $openedTag);
        }

        return $tagTree;
    }

    /**
     * Remove special character @ handle by template engine from value.
     * ex: <div :class=@{}></div> to <div :class={}></div>.
     */
    private function parseSpecialCharacter(string $chunk): string
    {
        return preg_replace('~@\{~', '{', $chunk);
    }

    /**
     * Render as string with Tag.
     * Return original template string prior to be parsed.
     */
    public function toLoadableString(string $region = self::TOP_TAG): string
    {
        $res = [];
        foreach ($this->getTagTree($region)->getChildren() as $v) {
            if ($v instanceof HtmlValue) {
                $res[] = $v->getHtml();
            } elseif ($v instanceof TagTree) {
                $tag = $v->getTag();
                $tagInnerStr = $this->toLoadableString($tag);
                $res[] = $tagInnerStr === ''
                    ? '{$' . $tag . '}'
                    : '{' . $tag . '}' . $tagInnerStr . '{/' . $tag . '}';
            } else {
                throw (new Exception('Value class has no save support'))
                    ->addMoreInfo('value_class', get_class($v));
            }
        }

        return implode('', $res);
    }

    public function renderToHtml(string $region = null): string
    {
        return $this->renderTagTreeToHtml($this->getTagTree($region ?? self::TOP_TAG));
    }

    protected function renderTagTreeToHtml(TagTree $tagTree): string
    {
        $res = [];
        foreach ($tagTree->getChildren() as $v) {
            if ($v instanceof HtmlValue) {
                $res[] = $v->getHtml();
            } elseif ($v instanceof TagTree) {
                $res[] = $this->renderTagTreeToHtml($v);
            } elseif ($v instanceof self) {
                $res[] = $v->renderToHtml();
            } else {
                throw (new Exception('Unexpected value class'))
                    ->addMoreInfo('value_class', get_class($v));
            }
        }

        return implode('', $res);
    }

    private function cloneTagTrees(array $tagTrees): array
    {
        $res = [];
        foreach ($tagTrees as $k => $v) {
            $res[$k] = $v->clone($this);
        }

        return $res;
    }

    private function assertHasTag(string $tag): void
    {
        if (!$this->hasTag($tag)) {
            throw (new Exception('Tag not found in template'))
                ->addMoreInfo('src', $this->source)
                ->addMoreInfo('tag', $tag)
                ->addMoreInfo('template_tags', array_keys($this->tagTrees));
        }
    }

    private function wrapTagName(string $name): string
    {
        return '{$' . $name . '}';
    }
}
