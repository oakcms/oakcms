<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\modules\widgets\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m170320_002026_widgetkit extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%widgetkit}}',
            [
                'id'   => $this->primaryKey(10),
                'name' => $this->string(255)->notNull(),
                'type' => $this->string(255)->notNull(),
                'data' => $this->text()->notNull(),
            ], $tableOptions
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%widgetkit}}');
    }
}
