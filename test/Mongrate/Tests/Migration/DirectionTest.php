<?php

namespace Mongrate\Tests\Migration;

use Mongrate\Model\Direction;

class DirectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Mongrate\Exception\InvalidDirectionException
     * @expectedExceptionMessage got: sideways
     */
    public function testThrowsExceptionIfInvalidDirectionGiven()
    {
        new Direction('sideways');
    }
}
