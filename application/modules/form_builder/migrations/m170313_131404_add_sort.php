<?php

use yii\db\Migration;
use yii\db\Schema;

class m170313_131404_add_sort extends Migration
{
    public function up()
    {
        $this->addColumn('{{%form_builder_forms}}', 'sort', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn('{{%form_builder_forms}}', 'sort');

        return false;
    }
}
