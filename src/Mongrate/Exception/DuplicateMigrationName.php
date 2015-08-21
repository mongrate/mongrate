<?php

namespace Mongrate\Exception;

use Mongrate\Migration\Name;

class DuplicateMigrationName extends \Exception
{
    public function __construct(Name $name)
    {
        parent::__construct('A migration with the name "' . $name . '" already exists.');
    }
}
