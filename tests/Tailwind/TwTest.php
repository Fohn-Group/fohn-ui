<?php

declare(strict_types=1);
/**
 * Tws test.
 */

namespace Fohn\Ui\Tests\Tailwind;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\Tailwind\Tw;
use PHPUnit\Framework\TestCase;

class TwTest extends TestCase
{
    public function testTw(): void
    {
        $twFrom = Tw::from(['a', 'b', 'c', 'd']);

        $this->assertSame(['a', 'b', 'c', 'd'], $twFrom());

        $twFrom->merge(['e', 'f']);
        $this->assertSame(['a', 'b', 'c', 'd', 'e', 'f'], $twFrom());

        $twOf = Tw::of('a');
        $this->assertSame(['a'], $twOf());

        $this->assertSame('a b c d e f', trim($twFrom->toString()));
        $this->assertSame('a', trim($twOf->toString()));
        $this->assertSame('a-b-c-d-e-f', trim($twFrom->toString(function (string $output, string $utility) {
            return ($output ? $output . '-' : '') . $utility;
        })));
    }

    public function testTwFunction(): void
    {
        $tws = Tw::from(['a', 'b', 'c', 'd']);

        $tws->filter(function (string $utitlity) {
            return $utitlity !== 'c';
        });
        $this->assertSame(['a', 'b', 'd'], $tws());

        $tws->map(function (string $utility) {
            return 'md:' . $utility;
        });
        $this->assertSame(['md:a', 'md:b', 'md:d'], $tws());

        $tws->reduce(function (array $carry, string $utility) {
            return array_merge($carry, ['lg:' . preg_replace('/[a-z]{2}:/', '', $utility)]);
        }, $tws());
        $this->assertSame(['md:a', 'md:b', 'md:d', 'lg:a', 'lg:b', 'lg:d'], $tws());
    }

    public function testTwFromFunction(): void
    {
        $tws = Tw::from(['a', 'b', 'c', 'd']);

        $twsMerged = $tws->fromMerge(['e']);
        $this->assertSame(['a', 'b', 'c', 'd'], $tws());
        $this->assertSame(['a', 'b', 'c', 'd', 'e'], $twsMerged());

        $twsFiltered = $tws->fromFilter(function (string $utitlity) {
            return $utitlity !== 'c';
        });
        $this->assertSame(['a', 'b', 'c', 'd'], $tws());
        $this->assertSame(['a', 'b', 'd'], $twsFiltered());

        $twsMapped = $tws->fromMap(function (string $utility) {
            return 'md:' . $utility;
        });
        $this->assertSame(['a', 'b', 'c', 'd'], $tws());
        $this->assertSame(['md:a', 'md:b', 'md:c', 'md:d'], $twsMapped());

        $twsReduced = $tws->fromReduce(function (array $carry, string $utility) {
            return array_merge($carry, ['lg:' . preg_replace('/[a-z]{2}:/', '', $utility)]);
        }, $tws());
        $this->assertSame(['a', 'b', 'c', 'd'], $tws());
        $this->assertSame(['a', 'b', 'c', 'd', 'lg:a', 'lg:b', 'lg:c', 'lg:d'], $twsReduced());
    }

    public function testUtilityGenerator(): void
    {
        $tw = Tw::utility('grid-cols', '4');
        $this->assertSame('grid-cols-4', $tw);

        $tw = Tw::utility('ring-blue', '300', 'hover');
        $this->assertSame('hover:ring-blue-300', $tw);
    }

    public function testColourUtility(): void
    {
        $color = Tw::colour('primary', 'bg');
        $this->assertSame('bg-purple-700', $color);
        $color = Tw::colour('primary', 'bg', 'hover');
        $this->assertSame('hover:bg-purple-700', $color);

        $textColor = Tw::textColor('primary', 'hover');
        $this->assertSame('hover:text-purple-700', $textColor);

        $bgColor = Tw::bgColor('primary', 'hover');
        $this->assertSame('hover:bg-purple-700', $bgColor);

        $borderColor = Tw::borderColor('primary', 'hover');
        $this->assertSame('hover:border-purple-700', $borderColor);
    }

    public function testColourException(): void
    {
        $this->expectException(Exception::class);
        Tw::colour('notRealColor', 'bg');
    }
}
