<?php

namespace Mongrate\Migrations;

use Mongrate\Exception\MigrationNotImplementedException;
use Mongrate\Migration\Migration as MigrationHelper;
use Doctrine\MongoDB\Database;

/**
 * THIS IS ONLY AN EXAMPLE.
 *
 * Convert a user's address from an array of objects to one root object.
 */
class UpdateAddressStructure
{
    use MigrationHelper;

    private $arrayIndexesThatIndicateMigrated = ['streetFirstLine', 'streetSecondLine', 'city', 'country', 'postcode', 'state'];

    public function up(Database $db)
    {
        $collection = $db->selectCollection('Company');
        $companies = $collection->find();

        foreach ($companies as $company) {
            if (!isset($company['address']) || count($company['address']) === 0) {
                continue;
            }

            $upNeeded = true;
            foreach ($this->arrayIndexesThatIndicateMigrated as $index) {
                if (isset($company['address'][$index])) {
                    $upNeeded = false;
                    break;
                }

            }

            if (!$upNeeded) {
                continue;
            }

            $addressToUse = isset($company['address'][0]) ? $company['address'][0] : array_pop($company['address']);

            // Convert to object instead of an array of objects.
            $collection->update(
                ['_id' => $company['_id']],
                ['$set' => ['address' => $addressToUse]]
            );
        }
    }

    public function down(Database $db)
    {
        $collection = $db->selectCollection('Company');
        $companies = $collection->find();

        foreach ($companies as $company) {
            if (!isset($company['address']) || count($company['address']) === 0) {
                continue;
            }

            $downNeeded = false;
            foreach ($this->arrayIndexesThatIndicateMigrated as $index) {
                if (isset($company['address'][$index])) {
                    $downNeeded = true;
                    break;
                }

            }

            if (!$downNeeded) {
                continue;
            }

            // Convert back to array of objects.
            $collection->update(
                ['_id' => $company['_id']],
                ['$set' => ['address' => [$company['address']]]]
            );
        }
    }
}
