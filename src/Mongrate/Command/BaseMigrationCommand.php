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
        $this->setOutput($output);
        $this->migrationName = new Name($input->getArgument('name'));
        $this->setMigrationName($this->migrationName);

        $this->loadMigrationClass($this->migrationName);
    }

    /**
     * @param Name $className
     *
     * @return string
     */
    private function generateFullClassName(Name $className)
    {
        return 'Mongrate\Migrations\\' . $className;
    }

    /**
     * @param $migrationName
     */
    protected function setMigrationName(Name $migrationName)
    {
        $this->className = $migrationName;
        $this->fullClassName = $this->generateFullClassName($this->className);
    }

    /**
     * Loads the migration class using the className.
     *
     * @param Name $className
     *
     * @throws MigrationDoesntExist
     */
    protected function loadMigrationClass(Name $className)
    {
        $file = $this->service->getMigrationClassFileFromName($this->migrationName);
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new MigrationDoesntExist($className, $file);
        }
    }

    /**
     * Migrate up or down.
     *
     * @param Direction $direction
     */
    protected function migrate(Direction $direction)
    {
        $fullClassName = $this->fullClassName;
        $migration = new $fullClassName();

        $this->output->writeln('<info>Migrating ' . $direction . '...</info> <comment>' . $this->migrationName . '</comment>');

        if ($direction->isUp()) {
            $migration->up($this->service->getDatabase());
            $this->setMigrationApplied(true);
        } else {
            $migration->down($this->service->getDatabase());
            $this->setMigrationApplied(false);
        }

        $this->output->writeln('<info>Migrated ' . $direction . '</info>');
    }

    /**
     * Update the database to record whether or not the migration has been applied.
     *
     * @param boolean $isApplied
     */
    private function setMigrationApplied($isApplied)
    {
        $collection = $this->service->getAppliedCollection();
        $criteria = ['className' => (string) $this->migrationName];
        $newObj = ['$set' => ['className' => (string) $this->migrationName, 'isApplied' => $isApplied]];
        $collection->upsert($criteria, $newObj);
    }

    /**
     * @param OutputInterface $output
     *
     * @return $this
     */
    protected function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }
}
