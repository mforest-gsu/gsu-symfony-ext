<?php

declare(strict_types=1);

namespace Gsu\Symfony\Log;

use Gsu\Symfony\Exception\StackTrace;
use Gsu\Symfony\Utils\Timer;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Psr\Log\LoggerAwareInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Service\ResetInterface;

#[AsMonologProcessor]
final class ConsoleLogProcessor implements
    LoggerAwareInterface,
    EventSubscriberInterface,
    ResetInterface,
    ProcessorInterface
{
    use LoggerAwareTrait;


    /** @inheritdoc */
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => ['onConsoleCommand', 1],
            ConsoleEvents::ERROR => ['onConsoleError', 1],
            ConsoleEvents::TERMINATE => ['onConsoleTerminate', 1]
        ];
    }


    /** @var array<string,array{0:Timer,1:Command,2:InputInterface,3:OutputInterface}> $eventStack */
    private array $eventStack = [];

    /** @var array{0:Timer,1:Command,2:InputInterface,3:OutputInterface}|null $currentEvent */
    private array|null $currentEvent = null;


    /** @inheritdoc */
    public function __invoke(LogRecord $record): LogRecord
    {
        if ($this->currentEvent !== null) {
            list($timer, $command) = $this->currentEvent;
            $record->extra['pid'] = sprintf('%08.0d', getmypid());
            $record->extra['elapsed'] = $timer->getElapsed();
            $record->extra['command'] = $command->getName();
        }

        return $record;
    }


    /** @inheritdoc */
    public function reset(): void
    {
        $this->eventStack = [];
        $this->currentEvent = null;
    }


    /**
     * @param ConsoleCommandEvent $event
     * @return void
     */
    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();
        $input = $event->getInput();
        $output = $event->getOutput();

        if ($this->currentEvent !== null) {
            array_push(
                $this->eventStack,
                $this->currentEvent
            );
            $this->currentEvent = null;
        }

        if ($command !== null && $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->currentEvent = [
                (new Timer())->start(),
                $command,
                $input,
                $output
            ];

            $this->logNotice(sprintf(
                "Started with arguments: %s",
                json_encode(
                    $input instanceof ArgvInput
                        ? $input->getRawTokens(true)
                        : array_merge(
                            $input->getArguments(),
                            $input->getOptions()
                        ),
                    JSON_THROW_ON_ERROR
                )
            ));
        }
    }


    /**
     * @param ConsoleErrorEvent $event
     * @return void
     */
    public function onConsoleError(ConsoleErrorEvent $event): void
    {
        if ($this->currentEvent !== null) {
            $ex = $event->getError();
            foreach (StackTrace::getStackTrace($ex) as $l) {
                $this->logError($l);
            }
        }
    }


    /**
     * @param ConsoleTerminateEvent $event
     * @return void
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        if ($this->currentEvent !== null) {
            $this->logNotice(sprintf(
                "Finished with exit code: %d",
                $event->getExitCode()
            ));
        }

        $this->currentEvent = array_pop($this->eventStack);
    }
}
