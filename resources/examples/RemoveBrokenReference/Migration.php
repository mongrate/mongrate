<?php

namespace Mongrate\Migrations;

use Mongrate\Migration\Migration as MigrationHelper;
use Doctrine\MongoDB\Database;

/**
 * Test this migration against the .yml files with `./mongrate test TemplateImageDimensions`
 */
class RemoveBrokenReference
{
    use MigrationHelper;

    const NEW_CITY_ID = '553a2b522fdb44fd198b457e';

    public function up(Database $db)
    {
        $userCollection = $db->selectCollection('User');
        $user = $userCollection
                ->findOne(['_id' => new \MongoId('53f31b5ad1703ad4398b4567')]);
        // Replace the Reading city reference by London.
        $newCityReference = \MongoDBRef::create('City', new \MongoId(self::NEW_CITY_ID));

        $userCollection->update(
            ['_id' => new \MongoId('53f31b5ad1703ad4398b4567')],
            ['$set' => ['city' => $newCityReference]]
        );
    }

    public function down(Database $db)
    {
        // The down migration should not do anything.
    }
}
