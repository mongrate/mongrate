<?php

namespace Mongrate\Command;

use Doctrine\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;
use Mongrate\Migration\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Parser;

class BaseCommand extends Command
{
    /**
     * Configuration, either read from the `parameters.yml` file or given by a wrapper like
     * MongrateBundle.
     *
     * @var array
     */
    protected $params;

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
            $this->params = $params;
        } else {
            $this->params = $this->getDefaultConfigurationParams();
        }

        $this->cleanConfigurationParams();

        $this->setupDatabaseConnection();
    }

    private function getDefaultConfigurationParams()
    {
        $yaml = new Parser();
        return $yaml->parse(file_get_contents('config/parameters.yml'))['parameters'];
    }

    private function cleanConfigurationParams()
    {
        // Trim trailing slashes so this can be configured with or without trailing slashes without
        // it making a difference.
        $this->params['migrations_directory'] = rtrim($this->params['migrations_directory'], '/');
    }

    protected function setupDatabaseConnection()
    {
        $config = new Configuration();
        $conn = new Connection($this->params['mongodb_server'], [], $config);
        $this->db = $conn->selectDatabase($this->params['mongodb_db']);
    }

    protected function getMigrationClassFileFromName($name)
    {
        return $this->params['migrations_directory'] . '/' . $name . '/Migration.php';
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
