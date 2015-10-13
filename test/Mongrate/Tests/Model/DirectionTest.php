<?php

namespace Mongrate\Test\Model;

use Mongrate\Model\Direction;
use Mongrate\Enum\DirectionEnum;

class DirectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Mongrate\Exception\InvalidDirectionException
     */
    public function testConstructorValidatesDirection()
    {
        $newDirection = new Direction('side');
    }

    public function testToString()
    {
        $direction = new Direction(DirectionEnum::UP);
        $this->assertEquals(DirectionEnum::UP, $direction);
    }

    public function testIsUp()
    {
        $direction = new Direction(DirectionEnum::UP);
        $this->assertTrue($direction->isUp());
    }

    public function testIsUpFail()
    {
        $direction = new Direction(DirectionEnum::DOWN);
        $this->assertFalse($direction->isUp());
    }

    public function testIsDown()
    {
        $direction = new Direction(DirectionEnum::DOWN);
        $this->assertTrue($direction->isDown());
    }

    /**
     * Test to ensure up static method return an instance of Direction with 'up' direction.
     */
    public function testUp()
    {
        $direction = Direction::up();

        $this->assertTrue($direction instanceof Direction);
        $this->assertEquals(DirectionEnum::UP, $direction);
    }

    /**
     * Test to ensure down static method return an instance of Direction with 'down' direction.
     */
    public function testDown()
    {
        $direction = Direction::down();

        $this->assertTrue($direction instanceof Direction);
        $this->assertEquals(DirectionEnum::DOWN, $direction);
    }
}
