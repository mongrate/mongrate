<?php

namespace Mongrate\Migration;

use Mongrate\Exception\InvalidNameException;

/**
 * Value object representing a migration name.
 */
class Name
{
    /**
     * MongoDB database names are limited to 63 characters. YAML tests run against a database
     * called 'mongrate_test_NAME', so the name must not exceed 49 characters.
     */
    const MAX_NAME_LENGTH = 49;

    private $name;

    /**
     * @param  string $name
     * @throws InvalidNameException if the name given is invalid.
     */
    public function __construct($name)
    {
        $this->validate($name);

        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Ensure the migration name is acceptable.
     *
     * @throws InvalidNameException if the name given is invalid.
     */
    private function validate($name)
    {
        if (strlen($name) > self::MAX_NAME_LENGTH) {
            throw new InvalidNameException(sprintf(
                'Migration name cannot exceed %d characters, is %d: %s',
                self::MAX_NAME_LENGTH,
                strlen($name),
                $name
            ));
        }
    }
}
