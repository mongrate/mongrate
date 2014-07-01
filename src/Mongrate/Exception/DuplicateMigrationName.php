<?php

namespace Mongrate\Exception;

class DuplicateMigrationName extends \Exception
{
    public function __construct($name)
    {
        parent::__construct('A migration with the name "' . $name . '" already exists.');
    }
}
