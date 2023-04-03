<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests\Service;

use Fohn\Ui\Component\Table;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\HtmlTemplate;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tests\Concerns\MockView;
use Fohn\Ui\View;
use PHPUnit\Framework\TestCase;

class UiServiceTest extends TestCase
{
    public function testGenerateId(): void
    {
        $longName = 'fonh_view_view_view_view_view';
        $shortName = 'fohn';

        $this->assertSame('uig8quzkuK', Ui::service()->factoryId($longName));
        $this->assertSame('mAp86sLodh', Ui::service()->factoryId($shortName));
        $this->assertSame('uig8quzkuK-fohn', Ui::service()->factoryId($longName, 'fohn'));
        $this->assertSame('dEZ4PCB4Gc-fo', Ui::service()->factoryId($shortName, 'fo'));

        $this->assertSame(10, strlen(Ui::service()->factoryId($longName, '', 10)));
        $this->assertSame(20, strlen(Ui::service()->factoryId($longName, '', 20)));
    }

    public function testHasValidOptions(): void
    {
        $options = [
            'opt1' => '1',
            'opt2' => '2',
            'opt3' => '3',
        ];

        $this->assertTrue(Ui::service()->hasValidOptions($options, ['opt1', 'opt2', 'opt3']));
        $this->assertFalse(Ui::service()->hasValidOptions($options, ['opt1', 'opt3']));
        $this->assertTrue(Ui::service()->hasValidOptions($options, ['opt1', 'opt2', 'opt3', 'opt4']));
    }

    /**
     * @dataProvider classNameProvider
     */
    public function testGetFromClassName(string $value, string $className): void
    {
        $this->assertSame($value, Ui::service()->factoryViewName($className));
    }

    public function classNameProvider(): array
    {
        return [
            'test view name' => ['view', View::class],
            'test table name' => ['table', Table::class],
            'test button name' => ['button', View\Button::class],
            'test anonymous' => ['anonymous', get_class(new class() {})],
        ];
    }

    public function testMergeSeeds(): void
    {
        $s1 = ['classA', 'name' => 'A'];
        $s2 = ['classB', 'name' => 'B', 'id' => 'id'];

        $this->assertSame(['classA', 'name' => 'A', 'id' => 'id'], Ui::service()->mergeSeeds($s1, $s2));
        $this->assertSame(['classB', 'name' => 'B', 'id' => 'id'], Ui::service()->mergeSeeds($s2, $s1));
    }

    /**
     * @dataProvider decodeJsonProvider
     */
    public function testDecodeJson(array $expected, array $decode): void
    {
        $this->assertSame($expected, $decode);
    }

    public function decodeJsonProvider(): array
    {
        return [
            'decode nested' => [
                ['opt1' => '1', 'opt2' => ['a1' => 'a', 'a2' => '2']],
                Ui::service()->decodeJson('{"opt1": "1", "opt2": {"a1": "a", "a2": "2"}}'),
            ],
            'decode nested bigint' => [
                ['opt1' => '1', 'opt2' => ['a1' => 'a', 'a2' => ['b1' => (string) 2 ** 53]]],
                Ui::service()->decodeJson('{"opt1": "1", "opt2": {"a1": "a", "a2": {"b1":"9007199254740992"}}}'),
            ],
            'decode int' => [
                ['jsMaxInt' => (string) ((2 ** 53) - 1), 'jsBigInt' => (string) 2 ** 53],
                Ui::service()->decodeJson('{"jsMaxInt":"9007199254740991","jsBigInt":"9007199254740992"}'),
            ],
        ];
    }

    public function testEncodeJson(): void
    {
        // make sure encodeJson is encoding bigint value.
        $json = Ui::service()->encodeJson(['jsMaxInt' => (2 ** 53) - 1, 'jsBigInt' => 2 ** 53]);
        $this->assertSame('{"jsMaxInt":9007199254740991,"jsBigInt":"9007199254740992n"}', $json);
    }

    /**
     * @dataProvider htmlTagProvider
     */
    public function testBuildHtmlTag(string $expectedTag, string $resultTag): void
    {
        $this->assertSame($expectedTag, $resultTag);
    }

    public function htmlTagProvider(): array
    {
        $tagArray = [
            ['a', 'id' => 'id1', 'href' => '#1', [['i', ['class' => 'icon home'], '']]],
            ['a', 'id' => 'id2', 'href' => '#2', ['mark2']],
        ];

        return [
            'basic img' => [
                '<img src="foo.gif" width="20%"/>',
                Ui::service()->buildHtmlTag('img/', ['src' => 'foo.gif', 'width' => '20%']),
            ],
            'basic b' => [
                '<b>',
                Ui::service()->buildHtmlTag('b'),
            ],
            'basic b content' => [
                '<b>hello world</b>',
                Ui::service()->buildHtmlTag('b', 'hello world'),
            ],
            'div from empty' => [
                '<div>',
                Ui::service()->buildHtmlTag([]),
            ],
            'input with attrb' => [
                '<input type="text" name="test"/>',
                Ui::service()->buildHtmlTag('input/', ['type' => 'text', 'name' => 'test']),
            ],
            'attribute escaping' => [
                '<div foo="he&quot;llo">',
                Ui::service()->buildHtmlTag(['foo' => 'he"llo']),
            ],
            'content escaping' => [
                '<b>bold text &gt;&gt;</b>',
                Ui::service()->buildHtmlTag('b', 'bold text >>'),
            ],
            'tag substitution_1' => [
                '<a>link</a>',
                Ui::service()->buildHtmlTag('b', ['a'], 'link'),
            ],
            'tag substitution_2' => [
                '<a/>',
                Ui::service()->buildHtmlTag('b/', ['a']),
            ],
            'tag substitution_3' => [
                '</b>',
                Ui::service()->buildHtmlTag('/b'),
            ],
            'tag substitution_4' => [
                '</a>',
                Ui::service()->buildHtmlTag('/b', ['a']),
            ],
            'tag substitution_5' => [
                '</a>',
                Ui::service()->buildHtmlTag('/b', ['foo' => 'bar', 'a']),
            ],
            'tag attribute false' => [
                '<a></a>',
                Ui::service()->buildHtmlTag('a', ['foo' => false], ''),
            ],
            'tag attribute true' => [
                '<td nowrap></td>',
                Ui::service()->buildHtmlTag('td', ['nowrap' => true], ''),
            ],
            'tag attribute with content' => [
                '<a href="hello">click</a>',
                Ui::service()->buildHtmlTag('a', ['href' => 'hello'], 'click'),
            ],
            'tag attribute without content' => [
                '<a href="hello"></a>',
                Ui::service()->buildHtmlTag('a', ['href' => 'hello'], ''),
            ],
            'nested tag_1' => [
                '<a href="hello"><b>welcome</b></a>',
                Ui::service()->buildHtmlTag('a', ['href' => 'hello'], [['b', 'welcome']]),
            ],
            'nested tag_2' => [
                '<a href="hello"><b class="red">welcome</b></a>',
                Ui::service()->buildHtmlTag('a', ['href' => 'hello'], [['b', ['class' => 'red'], 'welcome']]),
            ],
            'nested tag_3' => [
                '<a href="hello"><b class="red"><i class="blue">welcome</i></b></a>',
                Ui::service()->buildHtmlTag('a', ['href' => 'hello'], [
                    ['b', ['class' => 'red'], [
                        ['i', ['class' => 'blue'], 'welcome'],
                    ]],
                ]),
            ],
            'nested tag_4' => [
                '<a href="hello">click <i>italic</i> text</a>',
                Ui::service()->buildHtmlTag('a', ['href' => 'hello'], ['click ', ['i', 'italic'], ' text']),
            ],
            'tag array' => [
                '<a id="id1" href="#1"><i class="icon home"></i></a><a id="id2" href="#2">mark2</a>',
                Ui::service()->buildHtmlTag($tagArray),
            ],
        ];
    }

    public function testFactoryFromSeed(): void
    {
        $view = Ui::factoryFromSeed([View::class, 'viewName' => 'Test', 'template' => new HtmlTemplate('')]);
        $this->assertSame('Test', $view->getViewName());
        $this->assertContainsOnlyInstancesOf(View::class, [$view]);
    }

    public function testFactoryFromSeedNotAbstractViewChildren(): void
    {
        $this->expectException(Exception::class);
        $view = Ui::factoryFromSeed([MockView::class]);
    }

    public function testFactoryFromSeedBadSeed0(): void
    {
        $this->expectException(Exception::class);
        $view = Ui::factoryFromSeed(['class' => View::class]);
    }

    public function testFactoryFromSeedBadSeedNotString(): void
    {
        $this->expectException(Exception::class);
        $view = Ui::factoryFromSeed([new View(['template' => new HtmlTemplate('')])]);
    }
}
