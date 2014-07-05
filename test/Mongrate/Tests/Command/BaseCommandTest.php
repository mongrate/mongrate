<?php

namespace Mongrate\Tests\Command;

use Doctrine\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;
use Symfony\Component\Yaml\Parser;

abstract class BaseCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $parametersFromYmlFile;
    protected $db;

    public function setUp()
    {
        $yaml = new Parser();
        $this->parametersFromYmlFile = $yaml->parse(file_get_contents(__DIR__ . '/parameters.yml'))['parameters'];

        $config = new Configuration();
        $conn = new Connection($this->parametersFromYmlFile['mongodb_server'], [], $config);
        $this->db = $conn->selectDatabase($this->parametersFromYmlFile['mongodb_db']);

        $this->wipeDatabase();
    }

    public function tearDown()
    {
        $this->wipeDatabase();
    }

    private function wipeDatabase()
    {
        $collections = $this->db->listCollections();

        foreach ($collections as $collection) {
            $collection->drop();
        }
    }
}
