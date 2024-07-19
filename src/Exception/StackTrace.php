<?php

declare(strict_types=1);

namespace Gsu\Symfony\Exception;

final class StackTrace
{
    /**
     * @param \Throwable $t
     * @return iterable<string>
     */
    public static function getStackTrace(\Throwable $t): iterable
    {
        yield from self::getStackTraceDetail($t);
    }


    /**
     * @param \Throwable $t
     * @param string[]|null $seen
     * @return iterable<string>
     */
    private static function getStackTraceDetail(
        \Throwable $t,
        ?array $seen = null
    ): iterable {
        yield sprintf(
            '%s%s: %s',
            is_array($seen) ? 'Caused by: ' :  '',
            get_class($t),
            $t->getMessage()
        );

        if ($seen === null) {
            $seen = [];
        }

        $file = $t->getFile();
        $line = $t->getLine();
        /** @var array<int,array<string,string>> $trace */
        $trace = $t->getTrace();
        $prev = $t->getPrevious();

        do {
            $current = "{$file}:{$line}";
            if (in_array($current, $seen, true)) {
                yield sprintf(' ... %d more', count($trace) + 1);
                break;
            } else {
                $seen[] = $current;
            }

            $traceFile =  $trace[0]['file'] ?? 'Unknown Source';
            $traceLine = intval(isset($trace[0]['file']) ? ($trace[0]['line'] ?? 0) : 0);
            if ($traceLine < 1) {
                $traceLine = null;
            }
            $traceClass = $trace[0]['class'] ?? null;
            $traceFunction = $trace[0]['function'] ?? null;

            yield sprintf(
                ' at %s%s%s(%s%s%s)',
                str_replace('\\', '.', ($traceClass ?? '')),
                is_string($traceClass) && is_string($traceFunction) ? '.' : '',
                str_replace('\\', '.', ($traceFunction ?? '(main)')),
                $line === null ? $file : basename($file),
                $line === null ? '' : ':',
                $line === null ? '' : $line
            );

            $file = $traceFile;
            $line = $traceLine;
            array_shift($trace);
        } while (count($trace) > 0);

        if ($prev !== null) {
            yield from self::getStackTraceDetail($prev, $seen);
        }
    }
}
