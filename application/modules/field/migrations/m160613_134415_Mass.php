<?php

use yii\db\Schema;
use yii\db\Migration;

class m160613_134415_Mass extends Migration {

    public function safeUp() {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        else {
            $tableOptions = null;
        }
        
        $connection = Yii::$app->db;

        try {
            $this->createTable('{{%field}}', [
                'id' => Schema::TYPE_PK . "",
                'name' => Schema::TYPE_STRING . "(255) NOT NULL",
                'slug' => Schema::TYPE_STRING . "(255) NOT NULL",
                'category_id' => Schema::TYPE_INTEGER . "(11)",
                'type' => Schema::TYPE_TEXT . "",
                'options' => Schema::TYPE_TEXT . "",
                'description' => Schema::TYPE_TEXT . "",
                'relation_model' => Schema::TYPE_STRING . "(55)",
                ], $tableOptions);

            $this->createIndex('category_id', '{{%field}}', 'category_id', 0);
            $this->createTable('{{%field_category}}', [
                'id' => Schema::TYPE_PK . "",
                'name' => Schema::TYPE_STRING . "(55)",
                'sort' => Schema::TYPE_INTEGER . "(11)",
                ], $tableOptions);

            $this->createTable('{{%field_value}}', [
                'id' => Schema::TYPE_PK . "",
                'field_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'variant_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'item_id' => Schema::TYPE_INTEGER . "(11)",
                'value' => Schema::TYPE_TEXT . "",
                'numeric_value' => Schema::TYPE_INTEGER . "(11)",
                ], $tableOptions);

            $this->createIndex('field_id', '{{%field_value}}', 'field_id', 0);
            $this->createIndex('variant_id', '{{%field_value}}', 'variant_id', 0);
            $this->createTable('{{%field_variant}}', [
                'id' => Schema::TYPE_PK . "",
                'field_id' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                'value' => Schema::TYPE_STRING . "(255)",
                'numeric_value' => Schema::TYPE_INTEGER . "(11) NOT NULL",
                ], $tableOptions);

            $this->createIndex('fk_field', '{{%field_variant}}', 'field_id', 0);
            $this->addForeignKey('fk_field_category_id', '{{%field}}', 'category_id', 'field_category', 'id');
            $this->addForeignKey('fk_field_value_field_id', '{{%field_value}}', 'field_id', 'field', 'id');
            $this->addForeignKey('fk_field_variant_field_id', '{{%field_variant}}', 'field_id', 'field', 'id');

        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage() . ' ';
        }
    }

    public function safeDown() {
        $connection = Yii::$app->db;
        try {
            $this->dropForeignKey('fk_field_category_id', '{{%field}}');
            $this->dropForeignKey('fk_field_value_field_id', '{{%field_value}}');
            $this->dropForeignKey('fk_field_value_field_id', '{{%field_value}}');
            $this->dropForeignKey('fk_field_value_field_id', '{{%field_value}}');
            $this->dropForeignKey('fk_field_variant_field_id', '{{%field_variant}}');
            $this->dropTable('{{%field}}');
            $this->dropTable('{{%field_category}}');
            $this->dropTable('{{%field_value}}');
            $this->dropTable('{{%field_variant}}');
            $transaction->commit();
        } catch (Exception $e) {
            echo 'Catch Exception ' . $e->getMessage() . ' ';
        }
    }

}
