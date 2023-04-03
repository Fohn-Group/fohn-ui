<?php

declare(strict_types=1);

/**
 * Helper for ContainerTrait in regards to View.
 */

namespace Fohn\Ui\Core;

use Fohn\Ui\View;

trait ViewHelperTrait
{
    /**
     * Initialize and add new object into parent View.
     *
     * $crud = Crud::addTo($app->layout, ['displayFields' => ['name']]);
     *   is equivalent to
     * $crud = $app->layout->addView(Crud::factory('displayFields' => ['name']);
     *
     * The first one design pattern is strongly recommended as it supports refactoring.
     *
     * @return static
     */
    public static function addTo(View $parent, array $properties = [], string $region = View::MAIN_TEMPLATE_REGION)// :static supported by PHP8+
    {
        // @phpstan-ignore-next-line
        return $parent->addView(static::factory($properties), $region);
    }

    /**
     * Add a View as object into a parent view.
     *
     * //public Icon $icon;
     * $this->icon = Icon::addSelfTo($view, new Icon(['iconName' => 'bi bi-home']));
     *
     * @return static
     */
    public static function addSelfTo(View $parent, View $self, string $region = View::MAIN_TEMPLATE_REGION)
    {
        self::assertIsSameClass(static::class, $self);

        // @phpstan-ignore-next-line
        return $parent->addView($self, $region);
    }

    /**
     * Add View in afterTemplateRegion.
     *
     * @return static
     */
    public static function addAfter(View $parent, array $properties = [])
    {
        // @phpstan-ignore-next-line
        return $parent->addView(static::factory($properties), View::AFTER_TEMPLATE_REGION);
    }

    /**
     * Add View in beforeTemplateRegion.
     *
     * @return static
     */
    public static function addBefore(View $parent, array $properties = [])
    {
        // @phpstan-ignore-next-line
        return $parent->addView(static::factory($properties), View::BEFORE_TEMPLATE_REGION);
    }

    private static function assertIsSameClass(string $className, View $object): void
    {
        $thisClass = new \ReflectionClass($className);
        $selfClass = new \ReflectionClass($object);

        if ($thisClass->getName() !== $selfClass->getName()) {
            throw (new Exception('addSelfTo method error: Trying to add $self using wrong class method.'))
                ->addMoreInfo('Using method from: ', $thisClass->getName())
                ->addMoreInfo('Using self object instance of: ', $selfClass->getName());
        }
    }
}
