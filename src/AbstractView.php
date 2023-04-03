<?php

declare(strict_types=1);

namespace Fohn\Ui;

use Fohn\Ui\Core\ContainerTrait;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\Core\InjectorTrait;
use Fohn\Ui\Service\Ui;

/**
 * Abstract view.
 * These are objects that does not render as html like Callbacks.
 *
 * @property AbstractView[] $elements
 *
 * @method View getOwner()
 */
abstract class AbstractView
{
    use ContainerTrait;
    use InjectorTrait;

    public const VIEW_CONTAINER = 'elements';
    public const ABSTRACT_CONTAINER = 'abstract';

    /** Marks instance as initialized. */
    private bool $initialized = false;

    public function __construct(array $defaults = [])
    {
        $this->injectDefaults($defaults);
    }

    /**
     * Create new View object with specify properties.
     * ClassName::factory($properties).
     *
     * @return mixed
     */
    public static function factory(array $properties = [])// :static supported by PHP8+
    {
        return Ui::factory(static::class, $properties);
    }

    /**
     * @return mixed
     */
    public static function factoryFromSeed(array $seed)
    {
        return Ui::factoryFromSeed($seed);
    }

    public function invokeInitRenderTree(bool $forceInit = false): self
    {
        if (!$this->initialized || $forceInit) {
            $this->initRenderTree();
        }

        return $this;
    }

    protected function initRenderTree(): void
    {
        $this->initialized = true;
    }

    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * Add an abstract view to Container.
     */
    public function addAbstractView(self $view, string $container = self::ABSTRACT_CONTAINER): self
    {
        $v = $this->addToContainer($view, $container);

        $v->invokeInitRenderTree();

        if (!$v->isInitialized()) {
            throw (new Exception('You should call parent::initRenderTree() when you override initializer'))
                ->addMoreInfo('obj', $v);
        }

        return $v;
    }
}
