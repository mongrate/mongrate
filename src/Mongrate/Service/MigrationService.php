<?php

namespace Mongrate\Service;

use Doctrine\MongoDB\Configuration as DoctrineConfiguration;
use Doctrine\MongoDB\Connection;
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

    /**
     * @var \Doctrine\MongoDB\Database
     */
    private $database;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->setupDatabaseConnection();
    }

    private function setupDatabaseConnection()
    {
        $connection = new Connection(
            $this->configuration->getDatabaseServerUri(),
            [],
            new DoctrineConfiguration()
        );
        $this->database = $connection->selectDatabase($this->configuration->getDatabaseName());
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

    public function getDatabase()
    {
        return $this->database;
    }

    public function selectCollection($collectionName)
    {
        return $this->database->selectCollection($collectionName);
    }

    /**
     * Get the collection which records whether a migration has been applied.
     *
     * @return \Doctrine\MongoDB\Collection
     */
    public function getAppliedCollection()
    {
        return $this->database->selectCollection('MongrateMigrations');
    }

    /**
     * Check if a migration has been applied.
     *
     * @param boolean $isApplied True if applied, false if not.
     */
    public function isMigrationApplied(Name $name)
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
}
