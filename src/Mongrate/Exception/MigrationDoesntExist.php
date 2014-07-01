<?php

namespace Mongrate\Exception;

class MigrationDoesntExist extends \Exception
{
    public function __construct($className, $file)
    {
        parent::__construct('There is no migration class called "' . $className . '" in "' . $file . '".');
    }
}
