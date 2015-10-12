<?php

namespace Mongrate\Tests\Migration;

use Mongrate\Model\Name;

class NameTest extends \PHPUnit_Framework_TestCase
{
    public function testValidNameDoesNotThrowException()
    {
        new Name('AValidName');
    }

    /**
     * @expectedException \Mongrate\Exception\InvalidNameException
     * @expectedExceptionMessage contains invalid characters: £"(
     */
    public function testThrowsExceptionIfContainsInvalidCharacters()
    {
        new Name('A£b"l(');
    }

    /**
     * @expectedException \Mongrate\Exception\InvalidNameException
     * @expectedExceptionMessage must not be empty
     */
    public function testThrowsExceptionIfEmpty()
    {
        new Name('');
    }

    /**
     * @expectedException \Mongrate\Exception\InvalidNameException
     * @expectedExceptionMessage cannot exceed 49 characters, is 61
     */
    public function testThrowsExceptionIfTooLong()
    {
        new Name('ALongNameThatExceedsTheMaximumLengthXXXXXXXXXXXXXXXXXXXXXXXXX');
    }

    /**
     * @expectedException \Mongrate\Exception\InvalidNameException
     * @expectedExceptionMessage must be a string, got integer
     */
    public function testThrowsExceptionIfNotAString()
    {
        new Name(42);
    }
}
