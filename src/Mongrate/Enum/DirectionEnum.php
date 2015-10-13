<?php

namespace Mongrate\Enum;

use Mongrate\Exception\InvalidDirectionException;

class DirectionEnum extends BaseEnum
{
    const UP = 'up';
    const DOWN = 'down';

    protected static function getConcreteClassValues()
    {
        return [
            self::UP,
            self::DOWN
        ];
    }

    /**
     * @param  string $value
     * @throws \InvalidArgumentException if not valid.
     */
    public static function validateValue($value)
    {
        try {
            parent::validateValue($value);
        } catch (\InvalidArgumentException $e) {
            $validOptions = '';
            foreach (self::getConcreteClassValues() as $direction) {
                if ($validOptions !== '') {
                    $validOptions .= ',';
                }
                $validOptions .= '"'.$direction.'" ';
            }
            throw new InvalidDirectionException(sprintf(
                'Direction must be %s got: %s',
                $validOptions,
                $value
            ));
        }
    }
}
