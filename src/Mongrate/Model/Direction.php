<?php

namespace Mongrate\Model;

use Mongrate\Exception\InvalidDirectionException;
use Mongrate\Enum\DirectionEnum;

/**
 * Value object representing a direction -- up or down -- to take a migration.
 */
class Direction
{
    private $direction;

    /**
     * @param string $direction
     * @throws InvalidDirectionException if the direction given is not valid.
     */
    public function __construct($direction)
    {
        DirectionEnum::validateValue($direction);

        $this->direction = $direction;
    }

    public static function up()
    {
        return new Direction(DirectionEnum::UP);
    }

    public static function down()
    {
        return new Direction(DirectionEnum::DOWN);
    }

    public function isUp()
    {
        return $this->direction === DirectionEnum::UP;
    }

    public function isDown()
    {
        return $this->direction === DirectionEnum::DOWN;
    }

    public function __toString()
    {
        return $this->direction;
    }
}
