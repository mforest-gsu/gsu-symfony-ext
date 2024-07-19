<?php

declare(strict_types=1);

namespace Gsu\Symfony\Exception;

class ArrayValuesError extends \RuntimeException
{
    /**
     * @param string|string[] $name
     * @param int $code
     * @param \Throwable|null|null $previous
     */
    public function __construct(
        string|array $name,
        int $code = 0,
        \Throwable|null $previous = null
    ) {
        parent::__construct(
            "Unable to get value: " . (is_array($name) ? implode(",", $name) : $name),
            $code,
            $previous
        );
    }
}
