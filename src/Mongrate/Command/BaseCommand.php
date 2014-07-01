<?php

namespace Mongrate\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Parser;

class BaseCommand extends Command
{
    protected $params;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $yaml = new Parser();
        $this->params = $yaml->parse(file_get_contents('config/parameters.yml'))['parameters'];
    }
}
