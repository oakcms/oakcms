<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\form_builder\migrations;

use yii\db\Migration;

class m170309_200111_form_builder extends Migration
{

    use \app\components\traits\TextTypesTrait;

    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        } else {
            $tableOptions = null;
        }

        try {

            $this->createTable('{{%form_builder_forms}}', [
                'id'     => $this->primaryKey(),
                'title'  => $this->string(255)->notNull(),
                'slug'   => $this->string(255)->notNull(),
                'sort'   => $this->integer(11),
                'status' => $this->boolean(),
                'data'   => $this->longText()->comment('(DC2Type:json_array)'),
            ], $tableOptions);

            $this->createTable('{{%form_builder_field}}', [
                'id'       => $this->primaryKey(),
                'form_id'  => $this->integer(11)->notNull(),
                'priority' => $this->integer(11)->defaultValue(0),
                'type'     => $this->string(255)->notNull(),
                'label'    => $this->string(255)->notNull(),
                'slug'     => $this->string(255)->notNull(),
                'options'  => $this->longText()->comment('(DC2Type:json_array)'),
                'roles'    => $this->longText()->comment('(DC2Type:simple_array)'),
                'data'     => $this->longText()->comment('(DC2Type:json_array)'),
            ], $tableOptions);

            $this->createTable('{{%form_builder_submission}}', [
                'id'      => $this->primaryKey(),
                'status'  => $this->smallInteger(6),
                'form_id' => $this->integer(11)->notNull(),
                'email'   => $this->string(255)->null(),
                'ip'      => $this->string(255)->notNull(),
                'created' => $this->integer(11),
                'data'    => $this->longText()->comment('(DC2Type:json_array)'),
            ], $tableOptions);

        } catch (Exception $e) {
            echo 'Catch Exception "' . $e->getMessage() . '" and rollBack this';
        }
    }

    public function safeDown()
    {
        try {
            $this->dropTable('{{%form_builder_forms}}');
            $this->dropTable('{{%form_builder_field}}');
            $this->dropTable('{{%form_builder_submission}}');
        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage() . ' and rollBack this';
        }
    }
}
