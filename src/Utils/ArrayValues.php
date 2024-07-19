<?php

declare(strict_types=1);

namespace Gsu\Symfony\Utils;

use Gsu\Symfony\Exception\ArrayValuesError;

class ArrayValues
{
    /** @var array<int,string> */
    public const TRUE_VALUES = ['1', 'ON', 'T', 'TRUE', 'X', 'Y', 'YES'];

    private TypedValues $typedValues;


    /**
     * @param mixed[] $values
     */
    public function __construct(
        private array &$values,
        TypedValues|null $typedValues = null
    ) {
        $this->typedValues = $typedValues ?? new TypedValues();
    }


    /**
     * @param string|string[] $name
     * @return bool
     */
    public function getBool(string|array $name): bool
    {
        return $this->getValue($name, $this->typedValues->getBool(...));
    }


    /**
     * @param string|string[] $name
     * @return bool|null
     */
    public function getBoolNull(string|array $name): bool|null
    {
        return $this->getValueNull($name, $this->typedValues->getBool(...));
    }


    /**
     * @param string|string[] $name
     * @return float
     */
    public function getFloat(string|array $name): float
    {
        return $this->getValue($name, $this->typedValues->getFloat(...));
    }


    /**
     * @param string|string[] $name
     * @return float|null
     */
    public function getFloatNull(string|array $name): float|null
    {
        return $this->getValueNull($name, $this->typedValues->getFloat(...));
    }


    /**
     * @param string|string[] $name
     * @return int
     */
    public function getInt(string|array $name): int
    {
        return $this->getValue($name, $this->typedValues->getInt(...));
    }


    /**
     * @param string|string[] $name
     * @return int|null
     */
    public function getIntNull(string|array $name): int|null
    {
        return $this->getValueNull($name, $this->typedValues->getInt(...));
    }


    /**
     * @param string|string[] $name
     * @return string
     */
    public function getString(string|array $name): string
    {
        return $this->getValue($name, $this->typedValues->getString(...));
    }


    /**
     * @param string|string[] $name
     * @return string|null
     */
    public function getStringNull(string|array $name): string|null
    {
        return $this->getValueNull($name, $this->typedValues->getString(...));
    }


    /**
     * @param string|string[] $name
     * @return mixed[]
     */
    public function getArray(string|array $name): array
    {
        return $this->getValue($name, $this->typedValues->getArray(...));
    }


    /**
     * @param string|string[] $name
     * @return mixed[]|null
     */
    public function getArrayNull(string|array $name): array|null
    {
        return $this->getValueNull($name, $this->typedValues->getArray(...));
    }


    /**
     * @template T of object
     * @param string|string[] $name
     * @param class-string<T> $class
     * @param (callable(mixed $value): T)|null $factory
     * @return T
     */
    public function getObject(
        string|array $name,
        string $class,
        callable|null $factory = null
    ): object {
        return $this->getValue($name, $this->typedValues->getObject($class, $factory));
    }


    /**
     * @template T of object
     * @param string|string[] $name
     * @param class-string<T> $class
     * @param (callable(mixed $value): T|null)|null $factory
     * @return T|null
     */
    public function getObjectNull(
        string|array $name,
        string $class,
        callable|null $factory = null
    ): object|null {
        return $this->getValueNull($name, $this->typedValues->getObject($class, $factory));
    }


    /**
     * @template T
     * @param string|string[] $name
     * @param (callable(mixed $v, string $n): T) $getTypedValue
     * @return T
     */
    public function getValue(
        string|array $name,
        callable $getTypedValue
    ): mixed {
        return $this->getValueNull($name, $getTypedValue) ?? throw new ArrayValuesError($name);
    }


    /**
     * @template T
     * @param string|string[] $name
     * @param (callable(mixed $v, string $n): T) $getTypedValue
     * @return T|null
     */
    public function getValueNull(
        string|array $name,
        callable $getTypedValue
    ): mixed {
        $names = is_array($name) ? $name : [$name];
        foreach ($names as $n) {
            if (!isset($this->values[$n])) {
                continue;
            }

            try {
                $value = $this->values[$n] ?? null;
                return !is_null($value)
                    ? $getTypedValue($value, $n)
                    : null;
            } catch (\Throwable $t) {
                throw new ArrayValuesError($n, 0, $t);
            }
        }

        return null;
    }
}
