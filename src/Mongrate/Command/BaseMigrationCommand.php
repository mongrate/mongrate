<?php

namespace Mongrate\Command;

use Mongrate\Exception\MigrationDoesntExist;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class BaseMigrationCommand extends BaseCommand
{
    protected $className;

    protected $fullClassName;

    protected $output;

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
}
