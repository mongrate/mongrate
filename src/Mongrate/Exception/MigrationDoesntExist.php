<?php

namespace Mongrate\Exception;

use Mongrate\Migration\Name;

class MigrationDoesntExist extends \Exception
{
    public function __construct(Name $className, $file)
    {
        parent::__construct('There is no migration class called "' . $className . '" in "' . $file . '".');
    }
}
