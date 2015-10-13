<?php

namespace Mongrate\Enum;

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
}
