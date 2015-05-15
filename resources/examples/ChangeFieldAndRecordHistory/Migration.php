<?php

namespace Mongrate\Migrations;

use Mongrate\Migration\Migration as MigrationHelper;
use Doctrine\MongoDB\Database;

/**
 * THIS IS AN EXAMPLE.
 *
 * Change a document's field, and record the change in the document's history. This example
 * demonstrates using `$exists` in the `input-verifier.yml` and `down-verifier.yml`.
 *
 * Test this migration against the .yml files with `./mongrate test ChangeFieldAndRecordHistory`
 */
class ChangeFieldAndRecordHistory
{
    use MigrationHelper;

    const OLD_RATE = 17.5;

    const NEW_RATE = 20.0;

    public function up(Database $db)
    {
        $history = $this->createHistory(self::NEW_RATE, self::OLD_RATE);

        $db->selectCollection('Item')->update(
            ['vatTaxRate' => self::OLD_RATE],
            ['$set' => ['vatTaxRate' => self::NEW_RATE], '$push' => ['history' => $history]],
            ['multiple' => true]
        );
    }

    public function down(Database $db)
    {
        $history = $this->createHistory(self::OLD_RATE, self::NEW_RATE);

        $db->selectCollection('Item')->update(
            ['vatTaxRate' => self::NEW_RATE],
            ['$set' => ['vatTaxRate' => self::OLD_RATE], '$push' => ['history' => $history]],
            ['multiple' => true]
        );
    }

    private function createHistory($toRate, $fromRate)
    {
        return [
            'info' => 'Changed VAT tax rate to ' . $toRate . '%, from ' . $fromRate . '%.',
            'date' => new \MongoDate(),
        ];
    }
}
