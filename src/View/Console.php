<?php
/**
 * Console.
 */

declare(strict_types=1);

namespace Fohn\Ui\View;

use Fohn\Ui\Callback\ServerEvent;
use Fohn\Ui\Core\Exception;
use Fohn\Ui\Js\Jquery;
use Fohn\Ui\Js\JsRenderInterface;
use Fohn\Ui\Js\JsStatements;
use Fohn\Ui\Service\Ui;
use Fohn\Ui\Tailwind\Theme\Base;
use Fohn\Ui\View;
use Psr\Log\LoggerInterface;

class Console extends View implements LoggerInterface
{
    public static string $commandLabel = 'Executing: ';
    public static string $exitCommandLabel = 'Exit code: ';
    public static string $methodLabel = 'Method: ';
    public static string $endMethodLabel = 'Result: ';
    public string $htmlTag = 'pre';
    protected ServerEvent $serverSideEvent;

    /** Will start console on page load when true. */
    protected bool $autoStart = true;

    /** default text color utility. */
    public string $textColor = 'warning-light';
    protected string $beginHtmlTemplate;
    protected string $endHtmlTemplate;
    protected string $outputHtmlTemplate;

    protected function initRenderTree(): void
    {
        parent::initRenderTree();
        $this->serverSideEvent = ServerEvent::addAbstractTo($this);
        if ($this->autoStart) {
            $this->appendJsAction($this->run());
        }

        $this->initMsgsTemplate();
    }

    protected function initMsgsTemplate(): self
    {
        $this->outputHtmlTemplate = '<div class="my-2 text-sm italic">{message}</div>';
        $this->beginHtmlTemplate = '<div class="my-2 text-sm italic pt-2 pl-2 text-white">{message}</div>';
        $this->endHtmlTemplate = '<div class="my-2 text-sm italic border-b-2 border-dotted pb-2 pl-2 text-white">{message}</div>';

        return $this;
    }

    /**
     * Execute console command.
     */
    public function onRun(\Closure $fx): void
    {
        $this->serverSideEvent->onRequest(function () use ($fx) {
            try {
                $fx($this);
            } catch (\Throwable $e) {
                // todo set proper exception message
                $this->outputHtmlMsg('<div class="">{error}</div>', ['error' => Ui::renderException($e)]);
            }
        });
    }

    public function executeJavascript(JsRenderInterface $js): self
    {
        $this->serverSideEvent->executeJavascript($js);

        return $this;
    }

    /**
     * Return Js event that will fire console run method.
     */
    public function run(JsStatements $statements = null): JsRenderInterface
    {
        if (!$statements) {
            $statements = JsStatements::with([Jquery::withView($this)->text('')]);
        }

        return $this->serverSideEvent->start($statements);
    }

    /**
     * Output a single line to the console.
     */
    public function outputMsg(string $message): string
    {
        return $this->outputHtmlMsg($this->outputHtmlTemplate, ['message' => htmlspecialchars($message)]);
    }

    /**
     * Output un-escaped HTML line. Use this to send HTML.
     * Message may contain placeholder using {}.
     * The placeholder content will be replaced by the valueTags pair.
     * Ex: $this->outputHtmlMsg('My name is {last_name}', ['last_name' => 'Doe'])
     * will output 'My name is Doe'.
     */
    public function outputHtmlMsg(string $message, array $valueTags = []): string
    {
        $message = preg_replace_callback('~{([\w]+)}~', function ($match) use ($valueTags) {
            if (isset($valueTags[$match[1]])) {
                return $valueTags[$match[1]];
            }

            return '{' . $match[1] . '}'; // don't change the original message
        }, $message);

        $this->serverSideEvent->executeJavascript(Jquery::withView($this)->append(preg_replace('/\R+/', ' ', $message)));

        return $message;
    }

    /**
     * Executes command passing along escaped parameters.
     *
     * Will also stream stdout / stderr as the command executes.
     * once command terminates method will return the exit code.
     *
     * Example: $console->execute('ping', ['-c', '5', '8.8.8.8']);
     *
     * All arguments are escaped.
     */
    public function execute(string $command, array $params = []): int
    {
        $exitCode = 0;
        if (function_exists('proc_open')) {
            $this->outputHtmlMsg($this->beginHtmlTemplate, ['message' => self::$commandLabel . $command . ' ' . implode(' ', $params)]);
            $exitCode = $this->runProcess(...$this->getProcessResources($command, $params));
            $this->outputHtmlMsg($this->endHtmlTemplate, ['message' => self::$exitCommandLabel . $exitCode]);
        } else {
            $this->critical('Error: Can\'t execute ' . $command . \PHP_EOL . ' proc_open is not supported on this platform.');
        }

        return $exitCode;
    }

    public function runMethod(object $object, string $method, array $args = []): void
    {
        $this->outputHtmlMsg($this->beginHtmlTemplate, ['message' => self::$methodLabel . get_class($object) . '->' . $method]);
        $result = $object->{$method}(...$args);
        $this->outputHtmlMsg($this->endHtmlTemplate, ['message' => self::$endMethodLabel . Ui::service()->encodeJson($result)]);
    }

    /**
     * @param mixed $process
     * @param mixed $pipes
     */
    private function runProcess($process, $pipes): int
    {
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);
        // $pipes contain streams that are still open and not EOF
        while ($pipes) {
            $read = $pipes;
            $write = [];
            $except = [];
            if (stream_select($read, $write, $except, 2) === false) {
                throw new Exception('stream_select() returned false.');
            }

            $stat = proc_get_status($process);
            if (!$stat['running']) {
                proc_close($process);

                break;
            }

            foreach ($read as $f) {
                $data = rtrim((string) fgets($f));
                if ($data === '') {
                    continue;
                }
                if ($f === $pipes[2]) {
                    $this->error($data); // STDERR
                } else {
                    $this->info($data); // STDOUT
                }
            }
        }

        return $stat['exitcode']; // @phpstan-ignore-line
    }

    private function getProcessResources(string $command, array $params = []): array
    {
        $escParams = [];
        foreach ($params as $val) {
            $this->assertIsScalar($val);
            $escParams[] = escapeshellarg($val);
        }

        $command = escapeshellcmd($command);
        $spec = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']]; // we want stdout and stderr
        $pipes = null;
        $process = proc_open($command . ' ' . implode(' ', $escParams), $spec, $pipes);
        if (!is_resource($process)) {
            throw (new Exception('Command failed to execute'))
                ->addMoreInfo('exec', $command);
        }

        return [$process, $pipes];
    }

    /**
     * @param mixed $val
     */
    private function assertIsScalar($val): void
    {
        if (!is_scalar($val)) {
            throw (new Exception('Arguments must be scalar'))
                ->addMoreInfo('arg', $val);
        }
    }

    protected function beforeHtmlRender(): void
    {
        Ui::theme()::styleAs(Base::CONSOLE, [$this, $this->textColor]);
        parent::beforeHtmlRender();
    }

    // Methods below implements \Psr\Log\LoggerInterface
    /**
     * System is unusable.
     *
     * @param string $message
     */
    public function emergency($message, array $context = []): void
    {
        $this->outputHtmlMsg('<div class="pl-6 text-red-600">' . htmlspecialchars($message) . '</div>', $context);
    }

    /**
     * Action must be taken immediately.
     *
     * @param string $message
     */
    public function alert($message, array $context = []): void
    {
        $this->outputHtmlMsg('<div class="pl-6 text-red-600">' . htmlspecialchars($message) . '</div>', $context);
    }

    /**
     * Critical conditions.
     *
     * @param string $message
     */
    public function critical($message, array $context = []): void
    {
        $this->outputHtmlMsg('<div  class="pl-6 text-red-600">' . htmlspecialchars($message) . '</div>', $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     */
    public function error($message, array $context = []): void
    {
        $this->outputHtmlMsg('<div class="pl-6 text-red-500">' . htmlspecialchars($message) . '</div>', $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * @param string $message
     */
    public function warning($message, array $context = []): void
    {
        $this->outputHtmlMsg('<div class="pl-6 text-yellow-600">' . htmlspecialchars($message) . '</div>', $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     */
    public function notice($message, array $context = []): void
    {
        $this->outputHtmlMsg('<div class="pl-6 text-yellow-300">' . htmlspecialchars($message) . '</div>', $context);
    }

    /**
     * Interesting events.
     *
     * @param string $message
     */
    public function info($message, array $context = []): void
    {
        $this->outputHtmlMsg('<div class="pl-6 text-gray-400">' . htmlspecialchars($message) . '</div>', $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     */
    public function debug($message, array $context = []): void
    {
        $this->outputHtmlMsg('<div class="pl-6 text-pink-600">' . htmlspecialchars($message) . '</div>', $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     */
    public function log($level, $message, array $context = []): void
    {
        $this->{$level}($message, $context);
    }
}
