<?php

namespace Mongrate\Migrations;

use Mongrate\Exception\MigrationNotImplementedException;
use Mongrate\Migration\Migration as MigrationHelper;
use Doctrine\MongoDB\Database;

/**
 * Test this migration against the .yml files with `./mongrate test DeleteOldLogs`
 */
class DeleteOldLogs
{
    use MigrationHelper;

    public function up(Database $db)
    {
        $endOf2013 = new \MongoDate(strtotime('2013-12-31T23:23:59Z'));

        $db->selectCollection('Log')->remove(
            ['date' => ['$lte' => $endOf2013]],
            ['multi' => true]
        );
    }

    public function down(Database $db)
    {
        throw new MigrationNotImplementedException('"down" method not implemented in "DeleteOldLogs".');
    }
}
