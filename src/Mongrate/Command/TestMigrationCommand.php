<?php

namespace Mongrate\Command;

use Doctrine\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;
use Mongrate\Exception\MigrationDoesntExist;
use Mongrate\Migration\Direction;
use Mongrate\Migration\Name;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class TestMigrationCommand extends BaseCommand
{
    private $output;

    private static $matchNativeMongoClass = '/^Mongo(Id|Code|Date|Regex|BinData|Int32|Int64|DBRef|MinKey|MaxKey|Timestamp)\((.*)\)$/';

    protected function configure()
    {
        $this->setName('test')
            ->setDescription('Test a migration up and down.')
            ->addArgument('name', InputArgument::REQUIRED, 'The class name, formatted like "UpdateAddressStructure_20140523".')
            ->addArgument('upOrDown', InputArgument::OPTIONAL, 'Whether to test going up or down. If left blank, both are tested.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $className = new Name($input->getArgument('name'));
        $direction = $input->getArgument('upOrDown')
            ? new Direction($input->getArgument('upOrDown'))
            : null;

        $classFile = $this->getMigrationClassFileFromClassName($className);
        if (file_exists($classFile)) {
            require_once $classFile;
        } else {
            throw new MigrationDoesntExist($className, $classFile);
        }

        $config = new Configuration();
        $conn = new Connection($this->params['mongodb_server'], [], $config);
        $this->db = $conn->selectDatabase('mongrate_test_' . $className);

        if ($direction) {
            $this->test($direction, $className);
        } else {
            $this->test(Direction::up(), $className);
            $this->test(Direction::down(), $className);
        }
    }

    private function test(Direction $direction, Name $className)
    {
        $testsDirectory = $this->params['migrations_directory'] . '/' . $className . '/';
        $inputFile = $testsDirectory . $direction . '-input.yml';
        $verifierFile = $testsDirectory . $direction . '-verifier.yml';

        $this->addFixturesToDatabaseFromYamlFile($inputFile);
        $this->applyMigration($className, $direction);
        $this->verifyDatabaseAgainstYamlFile($verifierFile);
    }

    private function addFixturesToDatabaseFromYamlFile($fixturesFile)
    {
        $yaml = new Parser();
        $fixtures = $yaml->parse(file_get_contents($fixturesFile));

        foreach ($fixtures as $collectionName => $collectionFixtures) {
            $collection = $this->db->selectCollection($collectionName);

            // Start off with an empty collection by removing all rows with an empty query.
            $collection->remove([]);

            foreach ($collectionFixtures as $i => $collectionFixture) {
                $collectionFixture = array_map([$this, 'convertYmlStringToNativeMongoObjects'], $collectionFixture);
                $collectionFixture['_orderInTestYamlFile'] = $i;
                $collection->insert($collectionFixture);
            }
        }
    }

    private function applyMigration(Name $className, Direction $direction)
    {
        $fullClassName = 'Mongrate\Migrations\\' . $className;
        $migration = new $fullClassName();

        if ($direction->isUp()) {
            $this->output->writeln('<info>Testing ' . $className . ' going up.</info>');
            $migration->up($this->db);
        } elseif ($direction->isDown()) {
            $this->output->writeln('<info>Testing ' . $className . ' going down.</info>');
            $migration->down($this->db);
        }
    }

    private function verifyDatabaseAgainstYamlFile($verifierFile)
    {
        $yaml = new Parser();
        $verifier = $yaml->parse(file_get_contents($verifierFile));

        foreach ($verifier as $collectionName => $verifierObjects) {
            $collection = $this->db->selectCollection($collectionName);

            $verifierObjects = array_map([$this, 'convertYmlStringToNativeMongoObjects'], $verifierObjects);
            $verifierObjects = $this->normalizeObject($verifierObjects);
            $verifierObjectsJson = json_encode($verifierObjects);

            $actualObjects = array_values($collection->find(
                ['$query' => [], '$orderby' => ['_orderInTestYamlFile' => 1]],
                ['_id' => 0, '_orderInTestYamlFile' => 0]
            )->toArray());
            $actualObjects = $this->normalizeObject($actualObjects);
            $actualObjectsJson = json_encode($actualObjects);

            $isVerified = $this->areEqual($verifierObjects, $actualObjects);
            if ($isVerified) {
                $this->output->writeln('<info>Test passed.</info>');
            } else {
                $this->output->writeln('<error>Test failed.</error>');
                $this->output->writeln('<comment>Expected:</comment>');
                $this->output->writeln($verifierObjectsJson);
                $this->output->writeln('<comment>Actual:</comment>');
                $this->output->writeln($actualObjectsJson);
            }
        }
    }

    private function normalizeObject($object)
    {
        if (is_string($object) || is_int($object) || is_bool($object) || is_float($object) || is_null($object)) {
            return $object;
        } elseif (is_array($object)) {
            if (count($object) === 0) {
                return [];
            }

            // If the array uses numeric keys, keep the keys intact.
            // If the array uses string keys, sort them alphabetically.
            if (array_keys($object)[0] !== 0) {
                ksort($object);
            }

            foreach ($object as $key => &$value) {
                $value = $this->normalizeObject($value);
            }

            return $object;
        } elseif (is_object($object) && preg_match('/^Mongo/', get_class($object)) === 1) {
            return $object;
        } else {
            throw new \InvalidArgumentException('Unexpected object type: ' . var_dump($object, true));
        }
    }

    /**
     * Converts YML strings to native Mongo* objects. E.g. if a string is "MongoDate(123)", it will
     * be converted to a MongoDate object representing the Unix time "123". If an array is given,
     * this method is applied recursively to all of the array's values.
     *
     * @param  mixed $value
     * @return mixed
     */
    private function convertYmlStringToNativeMongoObjects($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'convertYmlStringToNativeMongoObjects'], $value);
        } else if (is_string($value)) {
            if (preg_match(self::$matchNativeMongoClass, $value, $matches) === 1) {
                $nativeMongoClass = 'Mongo' . $matches[1];
                return new $nativeMongoClass($matches[2]);
            }
            return $value;
        }

        return $value;
    }

    /**
     * Check if the two parameters are equal to each other.
     *
     * Supports the comparison of:
     * - strings, integers, booleans, floats
     * - null
     * - arrays, which are checked for equality recursively.
     * - MongoDate, MongoRegex, etc. objects.
     * - $exists in the 'expected' value, which will only check that the equivalent actual value is
     *   not null.
     *
     * @param  mixed   $expected
     * @param  mixed   $actual
     * @return boolean
     */
    private function areEqual($expected, $actual)
    {
        if (is_array($actual) && is_array($expected)) {
            if (count($actual) !== count($expected)) {
                return false;
            }

            if (array_keys($actual) !== array_keys($expected)) {
                return false;
            }

            foreach ($actual as $actualKey => $actualValue) {
                if (!$this->areEqual($expected[$actualKey], $actualValue)) {
                    return false;
                }
            }

            return true;
        }

        if (is_string($expected) && $expected === '$exists') {
            return $actual !== null;
        }

        // Compare after applying `json_encode()` because we are interested in the JSON
        // representation that will be put into MongoDB, not whether two PHP objects have the same
        // reference (two identical MongoDate objects would have different references, for example).
        return json_encode($expected) === json_encode($actual);
    }
}
