<?php

namespace Mongrate\Command;

use Mongrate\Exception\InvalidFixturesException;
use Mongrate\Exception\MigrationDoesntExist;
use Mongrate\Model\Direction;
use Mongrate\Model\Name;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class TestMigrationCommand extends BaseCommand
{
    private $input;

    private $output;

    private static $matchNativeMongoClass = '/^Mongo(Id|Code|Date|Regex|BinData|Int32|Int64|DBRef|MinKey|MaxKey|Timestamp)\((.*)\)$/';

    protected function configure()
    {
        $this->setName('test')
            ->setDescription('Test a migration up and down.')
            ->addArgument('name', InputArgument::REQUIRED, 'The class name, formatted like "UpdateAddressStructure_20140523".')
            ->addArgument('direction', InputArgument::OPTIONAL, 'Whether to test going up or down. If left blank, both are tested.')
            ->addOption('pretty', null, InputArgument::OPTIONAL, 'Whether to pretty-print the output if there is an error.', false)
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $name = new Name($input->getArgument('name'));
        $direction = $input->getArgument('direction')
            ? new Direction($input->getArgument('direction'))
            : null;

        $classFile = $this->service->getMigrationClassFileFromName($name);
        if (file_exists($classFile)) {
            require_once $classFile;
        } else {
            throw new MigrationDoesntExist($name, $classFile);
        }

        $this->service->switchToDatabase('mongrate_test_' . $name);

        if ($direction) {
            $this->test($name, $direction);
        } else {
            $this->test($name, Direction::up());
            $this->test($name, Direction::down());
        }
    }

    private function test(Name $name, Direction $direction)
    {
        $testsDirectory = $this->service->getConfiguration()->getMigrationsDirectory()
            . '/'
            . $name
            . '/';
        $inputFile = $testsDirectory . $direction . '-input.yml';
        $verifierFile = $testsDirectory . $direction . '-verifier.yml';

        $this->addFixturesToDatabaseFromYamlFile($inputFile);
        $this->applyMigration($name, $direction);
        $this->verifyDatabaseAgainstYamlFile($verifierFile);
    }

    private function addFixturesToDatabaseFromYamlFile($fixturesFile)
    {
        $fixtures = $this->getFixturesFromYamlFile($fixturesFile);

        foreach ($fixtures as $collectionName => $collectionFixtures) {
            $collection = $this->service->selectCollection($collectionName);

            // Start off with an empty collection by removing all documents with an empty query.
            $collection->remove([]);

            // Ensure there are no indexes that can prevent test input documents being inserted,
            // e.g. there may be a uniqueness index that is being added in the migration - so
            // we need to clean up the unique index that was added the last time the test migration
            // command was run.
            $collection->deleteIndexes();

            foreach ($collectionFixtures as $i => $collectionFixture) {
                $collectionFixture = array_map([$this, 'convertYmlStringToNativeMongoObjects'], $collectionFixture);
                $collectionFixture['_orderInTestYamlFile'] = $i;
                $collection->insert($collectionFixture);
            }
        }
    }

    private function applyMigration(Name $name, Direction $direction)
    {
        $migration = $this->service->createMigrationInstance($name, $this->output);

        if ($direction->isUp()) {
            $this->output->writeln('<info>Testing ' . $name . ' going up.</info>');
            $migration->up($this->service->getDatabase());
        } elseif ($direction->isDown()) {
            $this->output->writeln('<info>Testing ' . $name . ' going down.</info>');
            $migration->down($this->service->getDatabase());
        }
    }

    private function verifyDatabaseAgainstYamlFile($verifierFile)
    {
        $verifier = $this->getFixturesFromYamlFile($verifierFile);

        foreach ($verifier as $collectionName => $verifierObjects) {
            $collection = $this->service->selectCollection($collectionName);

            $verifierObjects = array_map([$this, 'convertYmlStringToNativeMongoObjects'], $verifierObjects);
            $verifierObjects = $this->normalizeObject($verifierObjects);

            $actualObjects = array_values($collection->find(
                ['$query' => [], '$orderby' => ['_orderInTestYamlFile' => 1]],
                ['_id' => 0, '_orderInTestYamlFile' => 0]
            )->toArray());
            $actualObjects = $this->normalizeObject($actualObjects);

            $isVerified = $this->areEqual($verifierObjects, $actualObjects);
            if ($isVerified) {
                $this->output->writeln('<info>Test passed.</info>');
            } else {
                $this->output->writeln('<error>Test failed.</error>');
                $this->output->writeln('<comment>Expected:</comment>');
                $this->output->writeln($this->getDocumentsAsPrintableJson($verifierObjects));
                $this->output->writeln('<comment>Actual:</comment>');
                $this->output->writeln($this->getDocumentsAsPrintableJson($actualObjects));
            }
        }
    }

    private function getDocumentsAsPrintableJson(array $documents)
    {
        $options = $this->input->getOption('pretty') ? JSON_PRETTY_PRINT : null;

        return json_encode($documents, $options);
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
     * be converted to a MongoDate object representing the Unix time "123", and "MongoDBRef(User,abc)"
     * will be converted to a MongoDBRef object representing the collection 'User' and ID 'abc'.
     * If an array is given, this method is applied recursively to all of the array's values.
     *
     * @param mixed $value
     * @return mixed
     */
    private function convertYmlStringToNativeMongoObjects($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'convertYmlStringToNativeMongoObjects'], $value);
        } elseif (is_string($value)) {
            if (preg_match(self::$matchNativeMongoClass, $value, $matches) === 1) {
                if ($matches[1] === 'DBRef') {
                    list($collection, $id) = explode(',', $matches[2]);
                    // Remove spaces to avoid invalid id when persisting.
                    $id = trim($id);
                    return \MongoDBRef::create($collection, new \MongoId($id));
                } else {
                    $nativeMongoClass = 'Mongo' . $matches[1];
                    return new $nativeMongoClass($matches[2]);
                }
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

    /**
     * @param string $fixturesFile
     * @return array
     */
    private function getFixturesFromYamlFile($fixturesFile)
    {
        $yaml = new Parser();
        $fixtures = $yaml->parse(file_get_contents($fixturesFile));

        if (!is_array($fixtures)) {
            throw new InvalidFixturesException('Your fixtures input or verified file must have an array at it\'s root.');
        }

        return $fixtures;
    }
}
