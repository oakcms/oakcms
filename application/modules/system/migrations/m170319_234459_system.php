<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\system\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m170319_234459_system extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable('{{%system_settings}}', [
            'id'          => $this->primaryKey(11),
            'param_name'  => $this->string(100)->notNull(),
            'param_value' => $this->string(255)->notNull(),
            'type'        => $this->string(255)->notNull(),
        ], $tableOptions);
        $this->createIndex('ix_settings_param_name', '{{%system_settings}}', 'param_name', false);

        $this->batchInsert('{{%system_settings}}',
            ["id", "param_name", "param_value", "type"],
            [
                [
                    'id'          => '1',
                    'param_name'  => 'indexing',
                    'param_value' => '1',
                    'type'        => 'checkbox',
                ],
                [
                    'id'          => '2',
                    'param_name'  => 'siteName',
                    'param_value' => 'OakCMS',
                    'type'        => 'textInput',
                ],
                [
                    'id'          => '3',
                    'param_name'  => 'googleAuthenticator',
                    'param_value' => '0',
                    'type'        => 'checkbox',
                ],
                [
                    'id'          => '4',
                    'param_name'  => 'language',
                    'param_value' => 'ru-RU',
                    'type'        => 'language',
                ],
                [
                    'id'          => '5',
                    'param_name'  => 'themeFrontend',
                    'param_value' => 'mu_mebel',
                    'type'        => 'getThemeFrontend',
                ],
                [
                    'id'          => '6',
                    'param_name'  => 'themeBackend',
                    'param_value' => 'base',
                    'type'        => 'getThemeBackend',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%system_settings}}');
    }
}
