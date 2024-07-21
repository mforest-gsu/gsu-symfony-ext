<?php

declare(strict_types=1);

namespace Gsu\Symfony\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

trait ExecuteCommandTrait
{
    /**
     * @param array<InputInterface|string[]> $input
     * @param OutputInterface $output
     * @param bool $returnOnFirstError
     * @return int
     */
    protected function executeCommands(
        array $input,
        OutputInterface $output,
        bool $returnOnFirstError = true
    ): int {
        $returnValue = Command::SUCCESS;
        foreach ($input as $in) {
            $exitCode = $this->executeCommand($in, $output);
            if ($exitCode !== Command::SUCCESS) {
                $returnValue = $exitCode;
                if ($returnOnFirstError) {
                    break;
                }
            }
        }

        return $returnValue;
    }


    /**
     * @param InputInterface|string[] $input
     * @param OutputInterface $output
     * @return int
     */
    protected function executeCommand(
        InputInterface|array $input,
        OutputInterface $output
    ): int {
        try {
            return $this->getApplication()?->doRun(
                is_array($input)
                    ? new ArgvInput([
                        $this->getApplication()->getName(),
                        ...$input
                    ])
                    : $input,
                $output
            ) ?? Command::FAILURE;
        } catch (\Throwable) {
            return Command::FAILURE;
        }
    }


    /**
     * @param string[] $input
     * @param (callable(string|\Stringable $message): void) $logger
     * @return int
     */
    protected function executeShellCommand(
        array $input,
        callable $logger
    ): int {
        $process = new Process($input, null, null, null, null);
        $process->start(fn (string $type, string $data) => $logger($data));
        $process->wait();
        return $process->getExitCode() ?? 1;
    }
}
