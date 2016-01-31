<?php

namespace Mongrate\Migration;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Helper methods for migrations.
 */
trait Migration
{
    private $output;

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }
}
