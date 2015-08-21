<?php

namespace Mongrate\Command;

use Doctrine\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;
use Mongrate\Migration\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Parser;

class BaseCommand extends Command
{
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
            $yaml = new Parser();
            $this->params = $yaml->parse(file_get_contents('config/parameters.yml'))['parameters'];
        }

        // Trim trailing slashes so this can be configured with or without trailing slashes without
        // it affecting anything.
        $this->params['migrations_directory'] = rtrim($this->params['migrations_directory'], '/');

        $this->setupDatabaseConnection();
    }

    protected function setupDatabaseConnection()
    {
        $config = new Configuration();
        $conn = new Connection($this->params['mongodb_server'], [], $config);
        $this->db = $conn->selectDatabase($this->params['mongodb_db']);
    }

    protected function getMigrationClassFileFromClassName($className)
    {
        return $this->params['migrations_directory'] . '/' . $className . '/Migration.php';
    }

    /**
     * Check if the migration has been applied.
     *
     * @param boolean $isApplied True if applied, false if not.
     */
    protected function isMigrationApplied(Name $className)
    {
        $collection = $this->getAppliedCollection();
        $criteria = ['className' => (string) $className];
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
