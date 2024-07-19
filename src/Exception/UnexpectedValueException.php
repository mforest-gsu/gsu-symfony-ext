<?php

declare(strict_types=1);

namespace Gsu\Symfony\Exception;

final class UnexpectedValueException extends \UnexpectedValueException
{
    /**
     * @param string $type
     * @param string $message
     * @param int $code
     * @param \Throwable|null|null $previous
     */
    public function __construct(
        public string $type,
        string $message = "",
        int $code = 0,
        \Throwable|null $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
