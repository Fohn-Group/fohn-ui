<?php

declare(strict_types=1);
/**
 * Test Input.
 */

namespace Fohn\Ui\Tests\Views;

use Fohn\Ui\Component\Form\Control\Calendar;
use Fohn\Ui\Component\Form\Control\Checkbox;
use Fohn\Ui\Component\Form\Control\Input;
use Fohn\Ui\Component\Form\Control\Number;
use Fohn\Ui\Component\Form\Control\Radio;
use Fohn\Ui\Core\Exception;

class InputTest extends \PHPUnit\Framework\TestCase
{
    protected function getInput(array $default = []): Input
    {
        $input = new MockInput(array_merge(['controlName' => 'inputCtrl'], $default));
        $input->invokeInitRenderTree();

        return $input;
    }

    public function testInput(): void
    {
        $input = $this->getInput();

        $this->assertFalse($input->isReadonly());
        $this->assertFalse($input->isDisabled());
        $this->assertFalse($input->isRequired());

        $input->readonly();
        $input->disabled();
        $input->required();

        $this->assertTrue($input->isReadonly());
        $this->assertTrue($input->isDisabled());
        $this->assertTrue($input->isRequired());

        $this->assertSame(['readonly' => 'true', 'disabled' => 'true', 'required' => 'true'], $input->getHtmlAttrs());
    }

    public function testInputAttribute(): void
    {
        $input = $this->getInput();

        $input->appendInputHtmlAttribute('placeholder', 'add value');
        $this->assertSame(['placeholder' => 'add value'], $input->getHtmlAttrs());

        $input->removeInputHtmlAttribute('placeholder');
        $this->assertSame([], $input->getHtmlAttrs());
    }

    public function testInputPlaceholder(): void
    {
        // test backward compatibility
        $input = $this->getInput(['placeholder' => 'add value']);
        $this->assertSame(['placeholder' => 'add value'], $input->getHtmlAttrs());
    }

    public function testHintCaption(): void
    {
        $input = $this->getInput();

        $input->setCaption('Input');
        $input->setHint('my hint');

        $this->assertSame('Input', $input->getCaption());
        $this->assertSame('my hint', $input->getHint());
    }

    public function testGetValue(): void
    {
        $input = $this->getInput();

        $input->setWithPostValue('a');
        $this->assertSame('a', $input->getValue());

        $input->setValue('b');
        $this->assertSame('b', $input->getInputValue());
    }

    public function testCalendarValue(): void
    {
        $dateInput = new Calendar(['controlName' => 'calendar']);

        $dateInput->setWithPostValue('2023-05-22');
        $date = \DateTime::createFromFormat('Y-m-d', '2023-05-22', new \DateTimeZone('UTC'));
        $this->assertSame($date, $dateInput->getValue());

        $dateInput->setValue(\DateTime::createFromFormat('Y-m-d', '2023-06-22', new \DateTimeZone('UTC')));
        $this->assertSame('2023-06-22', $dateInput->getInputValue());

        $dateTimeInput = new Calendar(['type' => 'datetime', 'format' => 'Y-m-d H:i']);
        $datetime = \DateTime::createFromFormat('Y-m-d', '2023-05-22 12:05', new \DateTimeZone('UTC'));
        $this->assertSame($datetime, $dateTimeInput->getValue());

        $dateTimeInput->setValue(\DateTime::createFromFormat('Y-m-d H:i', '2023-06-22 12:05', new \DateTimeZone('UTC')));
        $this->assertSame('2023-06-22 12:05', $dateTimeInput->getInputValue());
    }

    public function testGetRadioValue(): void
    {
        $radio = new Radio(['controlName' => 'radio']);
        $radio->setItems(['a' => 'A', 'b' => 'B']);

        $this->assertSame('a', $radio->getValue());

        $radio->setValue('b');
        $this->assertSame('b', $radio->getValue());
        $this->assertSame('b', $radio->getInputValue());

        $this->expectException(Exception::class);
        $radio->setValue('c');
    }

    public function testGetNumberValue(): void
    {
        $int = new Number(['controlName' => 'int']);
        $int->setValue(12);
        $this->assertSame('12', $int->getInputValue());

        $int->setWithPostValue('12');
        $this->assertSame(12, $int->getValue());

        $float = new Number(['controlName' => 'float', 'precision' => 2]);
        $float->setValue(12.34);
        $this->assertSame(12.34, $float->getInputValue());

        $float->setWithPostValue('22.34');
        $this->assertSame(22.34, $float->getValue());
    }

    public function testCheckboxValue(): void
    {
        $box = new Checkbox(['controlName' => 'box']);
        $box->setValue(true);
        $this->assertTrue($box->getValue());
        $box->setValue(false);
        $this->assertFalse($box->getValue());

        $box->setWithPostValue('1');
        $this->assertTrue($box->getValue());
    }
}
