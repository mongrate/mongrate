<?php

namespace Mongrate\Test\Enum;

use Mongrate\Enum\DirectionEnum;

class DirectionEnumTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException InvalidArgumentException
     */
    public function testValidateValueThrowException()
    {
        DirectionEnum::validateValue('UP');
    }

    public function testValidateValueSuccess()
    {
        DirectionEnum::validateValue('up');
    }

    public function testValidateValuesSuccess()
    {
        DirectionEnum::validateValues(['up', 'down']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testValidateValuesError()
    {
        DirectionEnum::validateValues(['up', 'side']);
    }

    public function testGetAllValues()
    {
        $availableDirections = DirectionEnum::getAllValues();

        $this->assertCount(2, $availableDirections);
    }
}
