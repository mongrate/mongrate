<?php

namespace Mongrate\Migrations;

use Mongrate\Exception\MigrationNotImplementedException;
use Mongrate\Migration\Migration as MigrationHelper;
use Doctrine\MongoDB\Database;

/**
 * Test this migration against the .yml files with `./mongrate test %class%`
 */
class %class%
{
    use MigrationHelper;

    public function up(Database $db)
    {
        throw new MigrationNotImplementedException('"up" method not implemented in "%class%".');
    }

    public function down(Database $db)
    {
        throw new MigrationNotImplementedException('"down" method not implemented in "%class%".');
    }
}
