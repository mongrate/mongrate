<?php

namespace Mongrate\Migrations;

use Mongrate\Migration\Migration as MigrationHelper;

class %class%
{
    use MigrationHelper;

    public function up()
    {
        throw new MigrationNotImplementedException('"up" method not implemented in "%class%".');
    }

    public function down()
    {
        throw new MigrationNotImplementedException('"down" method not implemented in "%class%".');
    }
}
