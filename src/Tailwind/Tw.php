<?php
/**
 * Tailwind CSS.
 */

declare(strict_types=1);

namespace Fohn\Ui\Tailwind;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Utilities\BackgroundTrait;
use Fohn\Ui\Tailwind\Utilities\BorderTrait;
use Fohn\Ui\Tailwind\Utilities\GenericTrait;

class Tw
{
    use BackgroundTrait;
    use BorderTrait;
    use GenericTrait;

    /** @var string[] Mapping generic position name to Tailwind position value. */
    public const POSITION_MAP = [
        'top' => 't',
        'bottom' => 'b',
        'left' => 'l',
        'right' => 'r',
        'top-bottom' => 'y',
        'bottom-top' => 'y',
        'left-right' => 'x',
        'right-left' => 'x',
    ];

    private array $tw;

    final private function __construct(array $tw)
    {
        $this->tw = $tw;
    }

    public static function from(array $tw): self
    {
        return new static($tw);
    }

    public static function of(string $x): self
    {
        return static::from([$x]);
    }

    public function __invoke(): array
    {
        return $this->get();
    }

    public function get(): array
    {
        return $this->tw;
    }

    public function map(\Closure $fn): self
    {
        $this->tw = array_map($fn, $this->tw);

        return $this;
    }

    // Return new instance using Map function.
    public function fromMap(\Closure $fn): self
    {
        return static::from(array_map($fn, $this->tw));
    }

    public function reduce(\Closure $fn, array $seed = []): self
    {
        $this->tw = array_reduce($this->tw, $fn, $seed);

        return $this;
    }

    /**
     * return new instance using reduce method.
     */
    public function fromReduce(\Closure $fn, array $seed = []): self
    {
        return static::from(static::from($this->tw)->reduce($fn, $seed)->get());
    }

    public function toString(\Closure $fn = null, string $seed = ''): string
    {
        if (!$fn) {
            $fn = function (string $output, string $utility): string {
                return $output . ' ' . $utility;
            };
        }

        return array_reduce($this->tw, $fn, $seed);
    }

    public function merge(array $utilities): self
    {
        $this->tw = array_merge($this->tw, $utilities);

        return $this;
    }

    /**
     * Return new instance using merge.
     */
    public function fromMerge(array $utilities): self
    {
        return static::from(static::from($this->tw)->merge($utilities)->get());
    }

    public function filter(\Closure $fn): self
    {
        $this->tw = array_filter($this->tw, $fn);

        return $this;
    }

    /**
     * Return new instance using filter.
     */
    public function fromFilter(\Closure $fn): self
    {
        return static::from(static::from($this->tw)->filter($fn)->get());
    }

    /**
     * Base Tailwind utility generator.
     */
    public static function utility(string $base, string $value, string $variant = ''): string
    {
        if ($variant && !in_array($variant, Ui::theme()->getSupportedVariants(), true)) {
            throw (new Exception('This variants is not supported by your theme.'))->addMoreInfo('variant', $variant);
        }

        $hyphen = ($base && $value || $base && $value === '0') ? '-' : '';

        return $variant ? $variant . ':' . $base . $hyphen . $value : $base . $hyphen . $value;
    }

    public static function colour(string $colour, string $utility, string $stateVariant = ''): string
    {
        $themeColors = Ui::theme()->getColours();
        if (!array_key_exists($colour, $themeColors)) {
            throw (new Exception('Color is not define in your theme.'))->addMoreInfo('colour', $colour);
        }

        return self::utility($utility, $themeColors[$colour], $stateVariant);
    }
}
