<?php

declare(strict_types=1);

namespace Fohn\Ui\Core;

use Fohn\Ui\AbstractView;
use Fohn\Ui\View;

/**
 * This trait makes it possible for you to add child objects
 * into your object.
 */
trait ContainerTrait
{
    /** @var View[] Element */
    private array $elements = [];
    /** @var AbstractView[] */
    private array $abstract = [];
    protected ?string $containerType = null;

    /** @var int[] */
    private array $nameCounts = [];

    /** Unique object id. The html Id attribute is build based on it. */
    private string $viewId = 'ui';

    /** Name of the object in owner's element array. */
    private string $viewShortId;

    /** Link to (parent) object into which we added this object. */
    private ?AbstractView $owner = null;

    public function getViewElements(): array
    {
        return $this->elements;
    }

    protected function setViewElements(array $views): void
    {
        foreach ($views as $view) {
            if ($view->containerType === self::ABSTRACT_CONTAINER) {
                throw new Exception('Trying to replace View with Abstract content');
            }
        }

        $this->elements = $views;
    }

    public function getViewId(): string
    {
        return $this->viewId;
    }

    public function getViewShortId(): ?string
    {
        return $this->viewShortId;
    }

    public function issetOwner(): bool
    {
        return $this->owner !== null;
    }

    public function getOwner(): AbstractView
    {
        return $this->owner;
    }

    /**
     * Return an array of owners for the specified View.
     * Starting with the immediate owner of the view and each owner of view-owner
     * until no more owner is set.
     */
    public function getOwners(AbstractView $view = null, array $owners = []): array
    {
        if (!$view) {
            $view = $this;
        }
        if ($view->issetOwner()) {
            $owner = $view->getOwner();
            $owners = array_merge([$owner], $owner->getOwners($owner, $owners));
        }

        return $owners;
    }

    /**
     * @return static
     */
    public function setOwner(AbstractView $owner)
    {
        if ($this->issetOwner()) {
            throw new Exception('Owner already set');
        }

        $this->owner = $owner;

        return $this;
    }

    /**
     * Removes object from parent, so that PHP's Garbage Collector can
     * dispose of it.
     */
    public function destroy(): void
    {
        if ($this->owner !== null) {
            $this->owner->removeElement($this->viewShortId, $this->containerType);

            // GC : remove reference to owner
            $this->owner = null;
        }
    }

    /**
     * Remove child element if it exists.
     */
    private function removeElement(string $shortId, string $type): void
    {
        unset($this->{$type}[$shortId]);
    }

    /**
     * Returns unique element name based on desired name.
     */
    private function getUniqueName(string $desiredName): string
    {
        if (!isset($this->nameCounts[$desiredName])) {
            $this->nameCounts[$desiredName] = 1;
            $postfix = '';
        } else {
            $postfix = '-' . (++$this->nameCounts[$desiredName]);
        }

        return $desiredName . $postfix;
    }

    /**
     * Extension to add() method which will perform linking of
     * the object with the current class.
     */
    private function addToContainer(AbstractView $element, string $container): AbstractView
    {
        if ($this->containerType === AbstractView::ABSTRACT_CONTAINER) {
            throw new Exception('Abstract type cannot add other object as children.');
        }

        $element->setOwner($this);
        $element->containerType = $container;
        $shortId = $this->getUniqueName($element->getDesiredId());

        $element->viewShortId = $shortId;
        $element->viewId = $this->viewId . '-' . $element->getViewShortId();
        $this->{$container}[$shortId] = $element;

        return $element;
    }

    /**
     * If name of the object is omitted then it's naturally to name them
     * after the class. You can specify a different naming pattern though.
     */
    private function getDesiredId(): string
    {
        // can be anything, but better to build meaningful name
        $name = static::class;
        if ((new \ReflectionClass($name))->isAnonymous()) {
            $name = '';
            foreach (class_parents(static::class) as $v) {
                if (!(new \ReflectionClass($v))->isAnonymous()) {
                    $name = $v;

                    break;
                }
            }
            $name .= '@anonymous';
        }

        return trim(preg_replace('~^Fohn\\\\[^\\\\]+\\\\|[^0-9a-z\x7f-\xfe]+~s', '-', mb_strtolower($name)), '-');
    }
}
