<?php

namespace Mongrate;

class Configuration
{
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getDatabaseName()
    {
        return $this->getParameter('mongodb_db');
    }

    public function getDatabaseServerUri()
    {
        return $this->getParameter('mongodb_server');
    }

    public function getMigrationsDirectory()
    {
        $directory = $this->getParameter('migrations_directory');

        // Trim trailing slashes so this can be configured with or without trailing slashes without
        // it making a difference to our own code.
        $directory = rtrim($directory, '/');

        return $directory;
    }

    private function getParameter($parameterName)
    {
        if (!isset($this->parameters[$parameterName])) {
            throw new \InvalidArgumentException('The parameter "' . $parameterName . '" does not exist.');
        }

        if (empty($this->parameters[$parameterName])) {
            throw new \InvalidArgumentException('The parameter "' . $parameterName . '" is empty.');
        }

        return $this->parameters[$parameterName];
    }
}
