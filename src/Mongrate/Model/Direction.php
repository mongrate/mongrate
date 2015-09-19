<?php

namespace Mongrate\Model;

use Mongrate\Exception\InvalidDirectionException;

/**
 * Value object representing a direction -- up or down -- to take a migration.
 */
class Direction
{
    private $direction;

    /**
     * @param  string $direction
     * @throws InvalidDirectionException if the direction given is not valid.
     */
    public function __construct($direction)
    {
        $this->validate($direction);

        $this->direction = $direction;
    }

    public static function up()
    {
        return new Direction('up');
    }

    public static function down()
    {
        return new Direction('down');
    }

    public function isUp()
    {
        return $this->direction === 'up';
    }

    public function isDown()
    {
        return $this->direction === 'down';
    }

    public function __toString()
    {
        return $this->direction;
    }

    /**
     * Ensure the direction is acceptable.
     *
     * @throws InvalidDirectionException if the direction given is not valid.
     */
    private function validate($direction)
    {
        if ($direction !== 'up' && $direction !== 'down') {
            throw new InvalidDirectionException(sprintf(
                'Direction must be "up" or "down", got: %s',
                $direction
            ));
        }
    }
}
