<?php

use yii\db\Schema;
use yii\db\Migration;

class m160513_121415_Mass extends Migration
{

    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        else {
            $tableOptions = null;
        }
        
        $connection = Yii::$app->db;

        try {
            $this->createTable('{{%filter}}', [
                'id' => Schema::TYPE_PK . "",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                'slug' => Schema::TYPE_STRING . "(155) NOT NULL",
                'sort' => Schema::TYPE_INTEGER . "(11)",
                'description' => Schema::TYPE_TEXT . "",
                'relation_field_name' => Schema::TYPE_STRING . "(55)",
                'is_filter' => "ENUM('yes', 'no') NULL DEFAULT  'no'",
                'type' => Schema::TYPE_STRING . "(55) NOT NULL",
                'relation_field_value' => Schema::TYPE_TEXT . " COMMENT 'PHP serialize'",
                ], $tableOptions);

            $this->createTable('{{%filter_relation_value}}', [
                'id' => Schema::TYPE_PK . "",
                'filter_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'value' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                ], $tableOptions);

            $this->createTable('{{%filter_value}}', [
                'id' => Schema::TYPE_PK . "",
                'filter_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'variant_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'item_id' => Schema::TYPE_INTEGER . "(11)",
                ], $tableOptions);

            $this->createIndex('variant_item', '{{%filter_value}}', 'variant_id,item_id', 1);

            $this->createTable('{{%filter_variant}}', [
                'id' => Schema::TYPE_PK . "",
                'filter_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'value' => Schema::TYPE_STRING . "(255)",
                'numeric_value' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                ], $tableOptions);

            $this->addForeignKey(
                'fk_variant', '{{%filter_value}}', 'variant_id', '{{%filter_variant}}', 'id', 'CASCADE', 'CASCADE'
            );
            
            $this->addForeignKey(
                'fk_filter', '{{%filter_variant}}', 'filter_id', '{{%filter}}', 'id', 'CASCADE', 'CASCADE'
            );
            
            $this->addForeignKey(
                'fk_filter', '{{%filter_value}}', 'filter_id', '{{%filter}}', 'id', 'CASCADE', 'CASCADE'
            );
            
        } catch (Exception $e) {
            echo 'Catch Exception "' . $e->getMessage() . '" and rollBack this';
        }
    }

    public function safeDown()
    {
        $connection = Yii::$app->db;
        try {
            $this->dropTable('{{%filter}}');
            $this->dropTable('{{%filter_relation_value}}');
            $this->dropTable('{{%filter_value}}');
            $this->dropTable('{{%filter_variant}}');
        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage() . ' and rollBack this';
        }
    }

}
