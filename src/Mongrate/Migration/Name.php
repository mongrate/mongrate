<?php

namespace Mongrate\Migration;

/**
 * Value object representing a migration name.
 */
class Name
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }
}
