<?php

namespace Mongrate\Service;

use Doctrine\MongoDB\Configuration as DoctrineConfiguration;
use Doctrine\MongoDB\Connection;
use Mongrate\Configuration;
use Mongrate\Exception\MigrationDoesntExist;
use Mongrate\Migration\Direction;
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
        $this->ensureMigrationExists($name);

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
     * Migrate up or down.
     *
     * @param Direction $direction
     */
    public function migrate(Name $name, Direction $direction, OutputInterface $output)
    {
        $this->loadMigrationClass($name);

        $fullClassName = $this->generateFullClassName($name);
        $migration = new $fullClassName();

        $output->writeln('<info>Migrating ' . $direction . '...</info> <comment>' . $name . '</comment>');

        if ($direction->isUp()) {
            $migration->up($this->database);
            $this->setMigrationApplied($name, true);
        } else {
            $migration->down($this->database);
            $this->setMigrationApplied($name, false);
        }

        $output->writeln('<info>Migrated ' . $direction . '</info>');
    }

    /**
     * Get a list of all migrations, sorted alphabetically.
     *
     * @return array<Name>
     */
    public function getAllMigrations()
    {
        $iterator = new \DirectoryIterator($this->configuration->getMigrationsDirectory());
        $migrations = [];

        foreach ($iterator as $file) {
            $file = (string) $file;
            if ($file === '.'|| $file === '..') {
                continue;
            }

            $name = new Name($file);

            $migrations[] = $name;
        }

        usort($migrations, function (Name $a, Name $b) {
            return strcmp((string) $a, (string) $b);
        });

        return $migrations;
    }

    /**
     * @param  Name $name
     * @throws MigrationDoesntExist
     */
    private function ensureMigrationExists(Name $name)
    {
        $file = $this->getMigrationClassFileFromName($name);

        if (!file_exists($file)) {
            throw new MigrationDoesntExist($name, $file);
        }
    }

    /**
     * Loads the migration class using the migration name.
     *
     * @param Name $name
     * @throws MigrationDoesntExist
     */
    private function loadMigrationClass(Name $name)
    {
        $this->ensureMigrationExists($name);

        require_once $this->getMigrationClassFileFromName($name);
    }

    /**
     * @param Name $name
     * @return string
     */
    private function generateFullClassName(Name $name)
    {
        return 'Mongrate\Migrations\\' . $name;
    }

    /**
     * Update the database to record whether or not the migration has been applied.
     *
     * @param boolean $isApplied
     */
    private function setMigrationApplied(Name $name, $isApplied)
    {
        $collection = $this->getAppliedCollection();
        $criteria = ['className' => (string) $name];
        $newObj = ['$set' => ['className' => (string) $name, 'isApplied' => $isApplied]];
        $collection->upsert($criteria, $newObj);
    }
}
