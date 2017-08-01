<?php

namespace Mongrate\Enum;

/**
 * Contains enumerable values.
 */
abstract class BaseEnum
{
    /**
     * Enums that extend BaseEnum must implement this method to return all
     * possible values that the enum may have. There should be no need to
     * make your method public - getAllValues is the public equivalent.
     *
     * @return array
     */
    protected static function getConcreteClassValues()
    {
        // We can't make this method abstract because it is static.
        // PHP 'strict standards' doesn't allow that.
        throw new \BadMethodCallException('getConcreteClassValues() must be implemented');
    }

    /**
     * @return array All possible values that the enum may have.
     */
    public static function getAllValues()
    {
        return array_combine(static::getConcreteClassValues(), static::getConcreteClassValues());
    }

    /**
     * @param  string $value
     * @throws \InvalidArgumentException if not valid.
     */
    public static function validateValue($value)
    {
        if (!in_array($value, static::getConcreteClassValues())) {
            throw new \InvalidArgumentException('Invalid value: ' .$value);
        }
    }

    /**
     * @param  array $values
     * @param  boolean $required If this is true, an exception is thrown if $values is empty.
     * @throws \InvalidArgumentException if not valid.
     */
    public static function validateValues(array $values, $required = true)
    {
        if ($required && count($values) === 0) {
            throw new \InvalidArgumentException('No values given');
        }

        foreach ($values as $value) {
            self::validateValue($value);
        }
    }
}
