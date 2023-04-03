<?php

declare(strict_types=1);

namespace Fohn\Ui\Tests;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\HtmlTemplate;
use Fohn\Ui\Js\Type\ObjectLiteral;
use PHPUnit\Framework\TestCase;

class HtmlTemplateTest extends TestCase
{
    protected function assertSameTemplate(string $expectedTemplateStr, HtmlTemplate $template): void
    {
        $expectedTemplate = new HtmlTemplate($expectedTemplateStr, false);

        $this->assertSame($expectedTemplate->toLoadableString(), $template->toLoadableString());
        $this->assertSame($expectedTemplate->renderToHtml(), $template->renderToHtml());
    }

    protected function assertSameTagTree(string $expectedTemplateStr, HtmlTemplate\TagTree $tagTree): void
    {
        $this->assertSameTemplate(
            $expectedTemplateStr,
            $tagTree->getParentTemplate()->cloneRegion($tagTree->getTag())
        );
    }

    public function testBasicInit(): void
    {
        $t = new HtmlTemplate('hello, {foo}world{/}', false);
        $t->set('foo', 'bar');

        $this->assertSameTemplate('hello, {foo}bar{/}', $t);
    }

    public function testGetTagTree(): void
    {
        $t = new HtmlTemplate('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}', false);
        $this->assertSameTagTree('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}', $t->getTagTree('_top'));

        $t = new HtmlTemplate('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}', false);
        $tagTreeFoo = $t->getTagTree('foo');
        $this->assertSameTagTree('hello', $tagTreeFoo);

        $tagTreeFoo->getChildren()[0]->set('good bye');
        $this->assertSameTemplate('{foo}good bye{/}, cruel {bar}world{/}. {foo}good bye{/}', $tagTreeFoo->getParentTemplate());
    }

    public function testGetTagRefNotFoundException(): void
    {
        $t = new HtmlTemplate('{foo}hello{/}');
        $this->expectException(Exception::class);
        $t->getTagTree('bar');
    }

    public function testLoadFromFile(): void
    {
        $t = new HtmlTemplate();
        $t->loadFromFile(__DIR__ . '/Concerns/fake-template.html');
        $this->assertTrue($t->hasTag('head'));
        $this->assertTrue($t->hasTag('title'));
        $this->assertFalse($t->hasTag('noTagName'));
        $this->assertFalse($t->hasTag('vueContent'));
        $this->assertTrue($t->hasTag(HtmlTemplate::MAIN_TEMPLATE_TAG));
        $this->assertTrue($t->hasTag(HtmlTemplate::BEFORE_TEMPLATE_TAG));
        $this->assertTrue($t->hasTag(HtmlTemplate::AFTER_TEMPLATE_TAG));

        $t = new HtmlTemplate('', false);
        $this->assertFalse($t->hasTag(HtmlTemplate::BEFORE_TEMPLATE_TAG));
        $this->assertFalse($t->hasTag(HtmlTemplate::AFTER_TEMPLATE_TAG));
    }

    public function testLoadFromFileNonExistentFileException(): void
    {
        $t = new HtmlTemplate();
        $this->expectException(Exception::class);
        $t->loadFromFile(__DIR__ . '/no-template-file.html');
    }

    public function testTryLoadFromFileNonExistentFileException(): void
    {
        $t = new HtmlTemplate();
        $this->assertNull($t->tryLoadFromFile(__DIR__ . '/no-template-file.html'));
    }

    public function testHasTag(): void
    {
        $t = new HtmlTemplate('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $this->assertTrue($t->hasTag('foo'));
        $this->assertTrue($t->hasTag('bar'));
        $this->assertFalse($t->hasTag('non_existent_tag'));
    }

    public function testSetAppendDel(): void
    {
        $t = new HtmlTemplate('{foo}hello{/} guys', false);

        // del tests
        $t->del('foo');
        $this->assertSameTemplate('{$foo} guys', $t);
        $t->tryDel('non_existent_tag');
        $this->assertSameTemplate('{$foo} guys', $t);

        // set tests
        $t->set('foo', 'Hello');
        $this->assertSameTemplate('{foo}Hello{/} guys', $t);
        $t->set('foo', 'Hi');
        $this->assertSameTemplate('{foo}Hi{/} guys', $t);
        $t->dangerouslySetHtml('foo', '<b>Hi</b>');
        $this->assertSameTemplate('{foo}<b>Hi</b>{/} guys', $t);
        $t->trySet('non_existent_tag', 'ignore this');
        $this->assertSameTemplate('{foo}<b>Hi</b>{/} guys', $t);
        $t->tryDangerouslySetHtml('non_existent_tag', '<b>ignore</b> this');
        $this->assertSameTemplate('{foo}<b>Hi</b>{/} guys', $t);

        // append tests
        $t->set('foo', 'Hi');
        $this->assertSameTemplate('{foo}Hi{/} guys', $t);
        $t->append('foo', ' and');
        $this->assertSameTemplate('{foo}Hi and{/} guys', $t);
        $t->dangerouslyAppendHtml('foo', ' <b>welcome</b> my');
        $this->assertSameTemplate('{foo}Hi and <b>welcome</b> my{/} guys', $t);
        $t->tryAppend('foo', ' dear');
        $this->assertSameTemplate('{foo}Hi and <b>welcome</b> my dear{/} guys', $t);
        $t->tryAppend('non_existent_tag', 'ignore this');
        $this->assertSameTemplate('{foo}Hi and <b>welcome</b> my dear{/} guys', $t);
        $t->tryDangerouslyAppendHtml('foo', ' and <b>smart</b>');
        $this->assertSameTemplate('{foo}Hi and <b>welcome</b> my dear and <b>smart</b>{/} guys', $t);
        $t->tryDangerouslyAppendHtml('non_existent_tag', '<b>ignore</b> this');
        $this->assertSameTemplate('{foo}Hi and <b>welcome</b> my dear and <b>smart</b>{/} guys', $t);
    }

    /**
     * Test special markup for Vue component.
     * {{varName}} should be rendered as is by template engine;.
     *
     * @{varName} should be rendered as {varName}.
     */
    public function testVueMarkupAsNoTag(): void
    {
        $t = new HtmlTemplate('<div><fohn-component #default="@{firstName}">{foo}hello{/} guys {{firstName}} </fohn-component></div>');

        $this->assertFalse($t->hasTag('templateProp'));
        $this->assertFalse($t->hasTag('firstName'));

        $this->assertSame('<div><fohn-component #default="{firstName}">hello guys {{firstName}} </fohn-component></div>', $t->renderToHtml());
    }

    public function testSetJsRenderInterface(): void
    {
        $t = new HtmlTemplate('<fohn-component :props="{$propsObject}"></fohn-component>');

        $t->setJs('propsObject', ObjectLiteral::set(['key1' => 'key1_value']));
        $this->assertSame('<fohn-component :props="{key1:\'key1_value\'}"></fohn-component>', $t->renderToHtml());

        $t = new HtmlTemplate('<fohn-component :props="\'\'"></fohn-component>');
        $t->trySetJs('non-specify-tag', ObjectLiteral::set(['key1' => 'key1_value']));

        $this->assertSame('<fohn-component :props="\'\'"></fohn-component>', $t->renderToHtml());
    }

    public function testClone(): void
    {
        $t = new HtmlTemplate('{foo}{inner}hello{/}{/} guys', false);

        $topClone1 = clone $t;
        $this->assertSameTemplate('{foo}{inner}hello{/}{/} guys', $topClone1);
        $topClone2 = $t->cloneRegion('_top');
        $this->assertSameTemplate('{foo}{inner}hello{/}{/} guys', $topClone2);
        $this->assertSameTemplate('{inner}hello{/}', $t->cloneRegion('foo'));
        $this->assertSameTemplate('{inner}hello{/}', $topClone1->cloneRegion('foo'));
        $this->assertSameTemplate('{inner}hello{/}', $topClone2->cloneRegion('foo'));
    }

    public function testRenderRegion(): void
    {
        $t = new HtmlTemplate('{foo}hello{/} guys');
        $this->assertSame('hello', $t->renderToHtml('foo'));
    }

    public function testParseDollarTags(): void
    {
        $t = new HtmlTemplate('{$foo} guys and {$bar} here', false);
        $t->set('foo', 'Hello');
        $t->set('bar', 'welcome');
        $this->assertSameTemplate('{foo}Hello{/} guys and {bar}welcome{/} here', $t);
    }

    public function testRegionAndMany(): void
    {
        $tags = [
            'foo' => 'foo_value',
            'bar' => 'bar_value',
        ];

        $t = new HtmlTemplate('{foo_Region}{$foo} - {$bar}{/} and {bar_Region}{$bar} - {$foo}{/}', false);

        $regions = $t->getRegionTagName('_Region');
        $this->assertSame(['foo_Region', 'bar_Region'], $regions);

        $regionFoo = $t->cloneRegion('foo_Region');
        $regionBar = $t->cloneRegion('bar_Region');

        $regionFoo->trySetMany($tags);
        $this->assertSame('foo_value - bar_value', $regionFoo->renderToHtml());

        $regionBar->trySetMany($tags);
        $this->assertSame('bar_value - foo_value', $regionBar->renderToHtml());
    }
}
