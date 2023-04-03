<?php

declare(strict_types=1);

namespace Fohn\Ui\AppTest;

use Fohn\Ui\Core\Exception;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Tw;
use Fohn\Ui\View\Button;
use Fohn\Ui\View\Console;
use Fohn\Ui\View\Heading\SectionHeader;
use Psr\Log\LoggerInterface;

require_once __DIR__ . '/../init-ui.php';

/** @var \Fohn\Ui\View $testRunClass */
$testRunClass = get_class(new class() extends \Fohn\Ui\View {
    public function generateReport(LoggerInterface $console): array
    {
        $console->log('info', 'Console Implements Logger Interface');
        $console->debug('debug {foo}', ['foo' => 'bar']);
        $console->emergency('emergency {foo}', ['foo' => 'bar']);
        $console->alert('alert {foo}', ['foo' => 'bar']);
        $console->critical('critical {foo}', ['foo' => 'bar']);
        $console->error('error {foo}', ['foo' => 'bar']);
        $console->warning('warning {foo}', ['foo' => 'bar']);
        $console->notice('notice {foo}', ['foo' => 'bar']);
        $console->info('info {foo}', ['foo' => 'bar']);

        return ['jsMaxInt' => (2 ** 53) - 1, 'jsBigInt' => 2 ** 53];
    }
});

// ////////////////////////
// / SHELL
// ///////////////////////

SectionHeader::addTo(Ui::layout(), ['title' => 'Console', 'subTitle' => 'Console can run on page load and/or when trigger by Javascript.'])
    ->setHeaderSize(2)->getHeaderView()->appendTailwind(Tw::textColor('info'));

SectionHeader::addTo(Ui::layout(), ['title' => 'Execute shell command'])
    ->setHeaderSize(4)->getHeaderView()->appendTailwind(Tw::textColor('info'));
$button = Button::addTo(Ui::layout(), ['type' => 'outline'])->setLabel('Re-run job');
$console = Console::addTo(Ui::layout());
Jquery::addEventTo($button, 'click')->execute($console->run());

$console->onRun(function (Console $console) use ($button) {
    $console->executeJavascript($button->disableUsingJavascript());
    $console->outputMsg('Executing process via execute...');
    sleep(1);
    $console->execute('/bin/pwd');
    sleep(1);
    $console->execute('ls', ['/var/www/html/public']);
    $console->executeJavascript($button->enableUsingJavascript());
});

// ////////////////////////
// / Class Method
// ///////////////////////

$section = SectionHeader::addTo(Ui::layout(), ['title' => 'Execute class method.']);
$section->setHeaderSize(4);
$section->getHeaderView()->appendTailwind(Tw::textColor('info'));
$button = Button::addTo(Ui::layout(), ['type' => 'outline'])->setLabel('Re-run job');
$console = Console::addTo(Ui::layout());
Jquery::addEventTo($button, 'click')->execute($console->run());

$console->onRun(function (Console $console) use ($testRunClass, $button) {
    $console->executeJavascript($button->disableUsingJavascript());
    $console->outputMsg('Calling object method via runMethod...');
    sleep(1);
    $console->runMethod(new $testRunClass(), 'generateReport', [$console]);
    $console->executeJavascript($button->enableUsingJavascript());
});

// ////////////////////////
// / Exception
// ///////////////////////

SectionHeader::addTo(Ui::layout(), ['title' => 'Can catch exception.'])
    ->setHeaderSize(4)->getHeaderView()->appendTailwind(Tw::textColor('info'));
$button = Button::addTo(Ui::layout(), ['type' => 'outline'])->setLabel('Re-run job');
$console = Console::addTo(Ui::layout());
Jquery::addEventTo($button, 'click')->execute($console->run());

$console->onRun(function (Console $console) {
    $console->outputMsg('Catching exception...');
    sleep(1);

    throw new Exception('this is a test');
});
