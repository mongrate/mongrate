<?php

namespace Mongrate\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Parser;

class BaseCommand extends Command
{
    protected $params;

    /**
     * @param string $name   Optional.
     * @param array  $params Optional. Can be used by wrappers like MongrateBunde (Symfony).
     *                       If not set, reads the params from 'config/parameters.yml'.
     */
    public function __construct($name = null, array $params = null)
    {
        parent::__construct($name);

        if (is_array($params)) {
            $this->params = $params;
        } else {
            $yaml = new Parser();
            $this->params = $yaml->parse(file_get_contents('config/parameters.yml'))['parameters'];
        }
    }
}
