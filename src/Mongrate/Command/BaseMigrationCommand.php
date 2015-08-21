<?php

namespace Mongrate\Command;

use Mongrate\Exception\MigrationDoesntExist;
use Mongrate\Migration\Direction;
use Mongrate\Migration\Name;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class BaseMigrationCommand extends BaseCommand
{
    protected $migrationName;

    protected $fullClassName;

    protected $output;

    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The class name, formatted like "UpdateAddressStructure_20140523".');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->migrationName = new Name($input->getArgument('name'));
        $this->fullClassName = 'Mongrate\Migrations\\' . $this->migrationName;

        $file = $this->getMigrationClassFileFromName($this->migrationName);
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new MigrationDoesntExist($this->migrationName, $file);
        }
    }

    /**
     * Migrate up or down.
     *
     * @param string $direction
     */
    protected function migrate(Direction $direction)
    {
        $fullClassName = $this->fullClassName;
        $migration = new $fullClassName();

        $this->output->writeln('<info>Migrating ' . $direction . '...</info> <comment>' . $this->migrationName . '</comment>');

        if ($direction->isUp()) {
            $migration->up($this->db);
            $this->setMigrationApplied(true);
        } else {
            $migration->down($this->db);
            $this->setMigrationApplied(false);
        }

        $this->output->writeln('<info>Migrated ' . $direction . '</info>');
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
        $criteria = ['className' => (string) $this->migrationName];
        $newObj = ['$set' => ['className' => (string) $this->migrationName, 'isApplied' => $isApplied]];
        $collection->upsert($criteria, $newObj);
    }
}
