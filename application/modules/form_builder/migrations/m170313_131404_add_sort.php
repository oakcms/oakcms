<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\form_builder\migrations;
use yii\db\Migration;
use yii\db\Schema;

class m170313_131404_add_sort extends Migration
{
    public function up()
    {
        $this->addColumn('{{%form_builder_field}}', 'sort', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn('{{%form_builder_field}}', 'sort');

        return false;
    }
}
