<?php

namespace Mongrate\Migrations;

use Mongrate\Migration\Migration as MigrationHelper;
use Doctrine\MongoDB\Database;

/**
 * Test this migration against the .yml files with `./mongrate test TemplateImageDimensions`
 */
class TemplateImageDimensions
{
    use MigrationHelper;

    public function up(Database $db)
    {
        $collection = $db->selectCollection('Template');
        $templates = $collection->find(
            [],
            ['files' => 1, 'id' => 1]
        );

        foreach ($templates as $template) {
            foreach ($template['files'] as $ii => $file) {
                $isImage = preg_match('/\.(jpg|png|gif)$/', $file['clientPath']) === 1;
                if (!$isImage) {
                    continue;
                }

                if (isset($file['attributes']['width']) && isset($file['attributes']['height'])) {
                    continue;
                }

                list($width, $height) = getimagesize($file['fileSystemUrl']);

                $log = sprintf('Got dimensions for image %s: %dx%d', $file['fileSystemUrl'], $width, $height);
                $this->output->writeln($log);

                $collection->update(
                    [
                        'id' => $template['id'],
                        'files.clientPath' => $file['clientPath'],
                    ],
                    [
                        '$set' => [
                            'files.' . $ii . '.attributes.width' => $width,
                            'files.' . $ii . '.attributes.height' => $height,
                        ]
                    ]
                );
            }
        }
    }

    public function down(Database $db)
    {
        // The down migration should not do anything.
    }
}
