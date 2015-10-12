<?php

namespace Mongrate\Model;

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

    /**
     * The name must be characters that are valid for a class name (excluding \ because namespaces
     * are not supported).
     */
    const NAME_VALID_CHARS_REGEX = 'a-zA-Z0-9_';

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
        if (!is_string($name)) {
            throw new InvalidNameException('Migration name must be a string, got ' . gettype($name));
        }

        if (strlen($name) === 0) {
            throw new InvalidNameException('Migration name must not be empty');
        }

        $validCharsRegex = '/^[' . self::NAME_VALID_CHARS_REGEX . ']+$/';
        if (preg_match($validCharsRegex, $name) === 0) {
            $invalidChars = preg_replace('/[' . self::NAME_VALID_CHARS_REGEX . ']/', '', $name);
            throw new InvalidNameException('Migration name contains invalid characters: ' . $invalidChars);
        }

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
