<?php

namespace Mongrate\Service;

use Mongrate\Configuration;
use Mongrate\Migration\Name;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationService
{
    /**
     * Configuration, either read from `/etc/mongrate.yml`, from the `parameters.yml` file or given
     * by a wrapper like MongrateBundle.
     *
     * @var \Mongrate\Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function ensureMigrationsDirectoryExists()
    {
        if (!is_dir($this->configuration->getMigrationsDirectory())) {
            throw new \RuntimeException(
                'The migrations directory does not exist. It is configured to be in: '
                . $this->configuration->getMigrationsDirectory()
            );
        }
    }

    public function getMigrationClassFileFromName(Name $name)
    {
        return sprintf(
            '%s/%s/Migration.php',
            $this->configuration->getMigrationsDirectory(),
            $name
        );
    }
}
