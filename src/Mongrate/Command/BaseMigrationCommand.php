<?php

namespace Mongrate\Command;

use Doctrine\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;
use Mongrate\Exception\MigrationDoesntExist;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class BaseMigrationCommand extends BaseCommand
{
    private $className;

    private $fullClassName;

    /**
     * @var \Doctrine\MongoDB\Database
     */
    private $db;

    private $output;

    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The class name, formatted like "UpdateAddressStructure_20140523".');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->className = $input->getArgument('name');
        $this->fullClassName = 'Mongrate\Migrations\\' . $this->className;

        $file = $this->getMigrationClassFileFromClassName($this->className);
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new MigrationDoesntExist($this->className, $file);
        }

        $this->setupDatabaseConnection();
    }

    private function setupDatabaseConnection()
    {
        $config = new Configuration();
        $conn = new Connection($this->params['mongodb_server'], [], $config);
        $this->db = $conn->selectDatabase($this->params['mongodb_db']);
    }

    /**
     * Migrate up or down.
     *
     * @param string $upOrDown
     */
    protected function migrate($upOrDown)
    {
        $fullClassName = $this->fullClassName;
        $migration = new $fullClassName();

        $this->output->writeln('<info>Migrating ' . $upOrDown . '...</info> <comment>' . $this->className . '</comment>');

        if ($upOrDown === 'up') {
            $migration->up($this->db);
            $this->setMigrationApplied(true);
        } else {
            $migration->down($this->db);
            $this->setMigrationApplied(false);
        }

        $this->output->writeln('<info>Migrated ' . $upOrDown . '</info>');
    }

    /**
     * Check if the migration has been applied.
     *
     * @param boolean $isApplied True if applied, false if not.
     */
    protected function isMigrationApplied()
    {
        $collection = $this->getAppliedCollection();
        $criteria = ['className' => $this->className];
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
     * @param string  $migration
     * @param boolean $isApplied
     */
    private function setMigrationApplied($isApplied)
    {
        $collection = $this->getAppliedCollection();
        $criteria = ['className' => $this->className];
        $newObj = ['$set' => ['className' => $this->className, 'isApplied' => $isApplied]];
        $collection->upsert($criteria, $newObj);
    }

    /**
     * Update the database to record whether or not the migration has been applied.
     *
     * @return \Doctrine\MongoDB\Collection
     */
    private function getAppliedCollection()
    {
        return $this->db->selectCollection('MongrateMigrations');
    }
}
