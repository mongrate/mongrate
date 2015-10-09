<?php

namespace Mongrate\Command;

use Mongrate\Configuration;
use Mongrate\Service\MigrationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Parser;

class BaseCommand extends Command
{
    protected $service;

    /**
     * @param string $name   Optional.
     * @param array  $params Optional. Can be used by wrappers like MongrateBunde (Symfony).
     *                       If not set, reads the params from 'config/parameters.yml'.
     */
    public function __construct($name = null, array $params = null)
    {
        parent::__construct($name);

        if (!is_array($params)) {
            $params = $this->getDefaultConfigurationParams();
        }

        $configuration = new Configuration($params);
        $this->service = new MigrationService($configuration);
    }

    private function getDefaultConfigurationParams()
    {
        $file = $this->getDefaultConfigurationParamsFile();
        $fileContent = file_get_contents($file);

        $yaml = new Parser();
        return $yaml->parse($fileContent)['parameters'];
    }

    private function getDefaultConfigurationParamsFile()
    {
        if (file_exists('config/parameters.yml')) {
            return 'config/parameters.yml';
        }

        if (file_exists('/etc/mongrate.yml')) {
            return '/etc/mongrate.yml';
        }

        throw new \RuntimeException('Config file not found in `config/parameters.yml` or `/etc/mongrate.yml`');
    }
}
