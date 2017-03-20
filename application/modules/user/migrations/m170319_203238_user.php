<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\modules\user\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m170319_203238_user extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';
        $this->createTable('{{%user}}', [
            'id'                        => $this->primaryKey(11),
            'created_at'                => $this->integer(11)->notNull(),
            'updated_at'                => $this->integer(11)->notNull(),
            'username'                  => $this->string(255)->notNull(),
            'auth_key'                  => $this->string(32)->null()->defaultValue(null),
            'email_confirm_token'       => $this->string(255)->null()->defaultValue(null),
            'password_hash'             => $this->string(255)->notNull(),
            'password_reset_token'      => $this->string(255)->null()->defaultValue(null),
            'email'                     => $this->string(255)->notNull(),
            'googleAuthenticator'       => $this->smallInteger(1)->notNull(),
            'googleAuthenticatorSecret' => $this->string(255)->notNull()->defaultValue(''),
            'status'                    => $this->smallInteger(6)->notNull()->defaultValue(0),
            'role'                      => $this->string(64)->notNull(),
        ], $tableOptions);
        $this->createIndex('idx-user-username', '{{%user}}', 'username', false);
        $this->createIndex('idx-user-email', '{{%user}}', 'email', false);
        $this->createIndex('idx-user-status', '{{%user}}', 'status', false);
        $this->createTable('{{%user_profile}}', [
            'user_id'    => $this->primaryKey(11),
            'firstname'  => $this->string(255)->null()->defaultValue(null),
            'middlename' => $this->string(255)->null()->defaultValue(null),
            'lastname'   => $this->string(255)->null()->defaultValue(null),
            'avatar'     => $this->string(255)->notNull(),
            'locale'     => $this->string(32)->notNull(),
            'gender'     => $this->integer(1)->null()->defaultValue(null),
        ], $tableOptions);
    }

    public function safeDown()
    {

        $this->dropTable('{{%user}}');
        $this->dropTable('{{%user_profile}}');

    }
}
