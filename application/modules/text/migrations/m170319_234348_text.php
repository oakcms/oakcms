<?php

/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */
namespace app\modules\text\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m170319_234348_text extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';
        $this->createTable('{{%texts}}', [
            'id'             => $this->primaryKey(11),
            'layout'         => $this->string(255)->notNull(),
            'slug'           => $this->string(255)->defaultValue(''),
            'where_to_place' => $this->string(10)->notNull(),
            'links'          => $this->text()->notNull(),
            'status'         => $this->smallInteger(1)->notNull(),
            'order'          => $this->integer(11)->notNull(),
            'published_at'   => $this->integer(11)->notNull(),
            'created_at'     => $this->integer(11)->notNull(),
            'updated_at'     => $this->integer(11)->notNull(),
        ], $tableOptions);
        $this->createIndex('slug', '{{%texts}}', 'slug', false);
        $this->createTable('{{%texts_lang}}', [
            'id'       => $this->primaryKey(11),
            'texts_id' => $this->integer(11)->notNull(),
            'title'    => $this->string(255)->notNull(),
            'subtitle' => $this->string(500)->notNull(),
            'text'     => $this->text()->notNull(),
            'settings' => $this->text()->notNull(),
            'language' => $this->string(10)->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {

        $this->dropTable('{{%texts}}');
        $this->dropTable('{{%texts_lang}}');

    }
}
