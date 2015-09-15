<?php

namespace Mongrate\Command;

use Doctrine\MongoDB\Configuration as DoctrineConfiguration;
use Doctrine\MongoDB\Connection;
use Mongrate\Configuration;
use Mongrate\Migration\Name;
use Mongrate\Service\MigrationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Parser;

class BaseCommand extends Command
{
    /**
     * Configuration, either read from `/etc/mongrate.yml`, from the `parameters.yml` file or given
     * by a wrapper like MongrateBundle.
     *
     * @var \Mongrate\Configuration
     *
     * @todo - remove this in favour of directly accessing the service object.
     */
    protected $configuration;

    /**
     * @var \Doctrine\MongoDB\Database
     */
    protected $db;

    /**
     * @param string $name   Optional.
     * @param array  $params Optional. Can be used by wrappers like MongrateBunde (Symfony).
     *                       If not set, reads the params from 'config/parameters.yml'.
     */
    public function __construct($name = null, array $params = null)
    {
        parent::__construct($name);

        if (is_array($params)) {
            $params = $params;
        } else {
            $params = $this->getDefaultConfigurationParams();
        }

        $this->configuration = new Configuration($params);
        $this->service = new MigrationService($this->configuration);
        $this->setupDatabaseConnection();
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

    protected function setupDatabaseConnection()
    {
        $config = new DoctrineConfiguration();
        $conn = new Connection($this->configuration->getDatabaseServerUri(), [], $config);
        $this->db = $conn->selectDatabase($this->configuration->getDatabaseName());
    }

    protected function getMigrationClassFileFromName($name)
    {
        return sprintf(
            '%s/%s/Migration.php',
            $this->configuration->getMigrationsDirectory(),
            $name
        );
    }

    /**
     * Check if the migration has been applied.
     *
     * @param boolean $isApplied True if applied, false if not.
     */
    protected function isMigrationApplied(Name $name)
    {
        $collection = $this->getAppliedCollection();
        $criteria = ['className' => (string) $name];
        $record = $collection->find($criteria)->getSingleResult();

        if ($record === null) {
            return false;
        } else {
            return (bool) $record['isApplied'];
        }
    }

    /**
     * Update the database to record whether or not the migration has been applied.
     *
     * @return \Doctrine\MongoDB\Collection
     */
    protected function getAppliedCollection()
    {
        return $this->db->selectCollection('MongrateMigrations');
    }
}
