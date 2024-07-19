<?php

declare(strict_types=1);

namespace Gsu\Symfony\Utils;

use Gsu\Symfony\Exception\UnexpectedValueException;

class TypedValues
{
    /**
     * @param mixed $value
     * @return bool
     */
    public function getBool(mixed $value): bool
    {
        return match (true) {
            is_bool($value) => $value,
            is_scalar($value) => boolval($value),
            default => throw new UnexpectedValueException(gettype($value))
        };
    }


    /**
     * @param mixed $value
     * @return float
     */
    public function getFloat(mixed $value): float
    {
        return match (true) {
            is_double($value) => $value,
            is_scalar($value) => doubleval($value),
            default => throw new UnexpectedValueException(gettype($value))
        };
    }


    /**
     * @param mixed $value
     * @return int
     */
    public function getInt(mixed $value): int
    {
        return match (true) {
            is_int($value) => $value,
            is_scalar($value) => intval($value),
            default => throw new UnexpectedValueException(gettype($value))
        };
    }


    /**
     * @param mixed $value
     * @return string
     */
    public function getString(mixed $value): string
    {
        return match (true) {
            is_string($value) => $value,
            is_scalar($value) || $value instanceof \Stringable => strval($value),
            default => throw new UnexpectedValueException(gettype($value))
        };
    }


    /**
     * @param mixed $value
     * @return mixed[]
     */
    public function getArray(mixed $value): array
    {
        return match (true) {
            is_array($value) => $value,
            is_object($value) => get_object_vars($value),
            is_string($value) => JSON::decodeArray($value),
            default => throw new UnexpectedValueException(gettype($value))
        };
    }


    /**
     * @template T of object
     * @param class-string<T> $class
     * @param (callable(mixed $value): T)|null $factory
     * @return (callable(mixed $value): T)
     */
    public function getObject(
        string $class,
        callable|null $factory = null
    ): callable {
        return fn (mixed $v): object => match (true) {
            is_object($v) && is_a($v, $class) => $v,
            is_callable($factory) => $factory($v),
            default => throw new UnexpectedValueException(gettype($v))
        };
    }


    /**
     * @param mixed $value
     * @return resource
     */
    public function getResource(mixed $value): mixed
    {
        return is_resource($value)
            ? $value
            : throw new UnexpectedValueException(gettype($value));
    }
}
