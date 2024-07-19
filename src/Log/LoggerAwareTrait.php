<?php

declare(strict_types=1);

namespace Gsu\Symfony\Log;

use Psr\Log\LoggerAwareTrait as PsrLoggerAwareTrait;
use Psr\Log\LogLevel;

trait LoggerAwareTrait
{
    use PsrLoggerAwareTrait;


    /**
     * System is unusable.
     *
     * @param string|\Stringable|iterable<string|\Stringable> $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function logEmergency(
        string|\Stringable|iterable $message,
        array $context = []
    ): void {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }


    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string|\Stringable|iterable<string|\Stringable> $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function logAlert(
        string|\Stringable|iterable $message,
        array $context = []
    ): void {
        $this->log(LogLevel::ALERT, $message, $context);
    }


    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string|\Stringable|iterable<string|\Stringable> $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function logCritical(
        string|\Stringable|iterable $message,
        array $context = []
    ): void {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }


    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string|\Stringable|iterable<string|\Stringable> $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function logError(
        string|\Stringable|iterable $message,
        array $context = []
    ): void {
        $this->log(LogLevel::ERROR, $message, $context);
    }


    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string|\Stringable|iterable<string|\Stringable> $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function logWarning(
        string|\Stringable|iterable $message,
        array $context = []
    ): void {
        $this->log(LogLevel::WARNING, $message, $context);
    }


    /**
     * Normal but significant events.
     *
     * @param string|\Stringable|iterable<string|\Stringable> $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function logNotice(
        string|\Stringable|iterable $message,
        array $context = []
    ): void {
        $this->log(LogLevel::NOTICE, $message, $context);
    }


    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string|\Stringable|iterable<string|\Stringable> $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function logInfo(
        string|\Stringable|iterable $message,
        array $context = []
    ): void {
        $this->log(LogLevel::INFO, $message, $context);
    }


    /**
     * Detailed debug information.
     *
     * @param string|\Stringable|iterable<string|\Stringable> $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function logDebug(
        string|\Stringable|iterable $message,
        array $context = []
    ): void {
        $this->log(LogLevel::DEBUG, $message, $context);
    }


    /**
     * Logs with an arbitrary level.
     *
     * @param string $level
     * @param string|\Stringable|iterable<string|\Stringable> $message
     * @param mixed[] $context
     *
     * @return void
     */
    public function log(
        string $level,
        string|\Stringable|iterable $message,
        array $context = []
    ): void {
        if (!is_iterable($message)) {
            $message = [$message];
        }

        foreach ($message as $m) {
            $this->logger?->log($level, $m, $context);
        }
    }
}
