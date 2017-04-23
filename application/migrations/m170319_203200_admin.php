<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\migrations;

use yii\db\Migration;

class m170319_203200_admin extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%hits}}', [
            'hit_id' => $this->primaryKey(),
            'user_agent' => $this->string()->notNull(),
            'ip' => $this->string()->notNull(),
            'target_group' => $this->string()->notNull(),
            'target_pk' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
        $this->createIndex('hits_uigp_idx', '{{%hits}}', ['user_agent', 'ip', 'target_group', 'target_pk']);

        $this->createTable('{{%system_db_state}}', [
            'id'        => $this->primaryKey(11),
            'timestamp' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%admin_medias}}', [
            'media_id'         => $this->primaryKey(11)->unsigned(),
            'file_title'       => $this->char(126)->notNull()->defaultValue(''),
            'file_description' => $this->char(254)->notNull()->defaultValue(''),
            'file_meta'        => $this->char(254)->notNull()->defaultValue(''),
            'file_mimetype'    => $this->char(64)->notNull()->defaultValue(''),
            'file_type'        => $this->char(32)->notNull()->defaultValue(''),
            'file_url'         => $this->string(900)->notNull()->defaultValue(''),
            'file_url_thumb'   => $this->string(900)->notNull()->defaultValue(''),
            'file_params'      => $this->string(17500)->null()->defaultValue(null),
            'status'           => $this->smallInteger(1)->notNull()->defaultValue(1),
            'created_on'       => $this->datetime()->notNull(),
            'created_by'       => $this->integer(11)->notNull()->defaultValue(0),
            'modified_on'      => $this->datetime()->notNull(),
            'modified_by'      => $this->integer(11)->notNull()->defaultValue(0),
        ], 'ENGINE=MyISAM');

        $this->createIndex('i_published', '{{%admin_medias}}', 'status', false);

        $this->createTable('{{%admin_modules}}', [
            'module_id'                => $this->primaryKey(11),
            'name'                     => $this->string(64)->notNull(),
            'class'                    => $this->string(128)->notNull(),
            'bootstrapClass'           => $this->string(128)->notNull()->defaultValue(''),
            'isFrontend'               => $this->smallInteger(1)->notNull(),
            'controllerNamespace'      => $this->string(500)->notNull()->defaultValue(''),
            'viewPath'                 => $this->string(500)->notNull()->defaultValue(''),
            'isAdmin'                  => $this->smallInteger(1)->notNull(),
            'AdminControllerNamespace' => $this->string(500)->notNull()->defaultValue(''),
            'AdminViewPath'            => $this->string(500)->notNull()->defaultValue(''),
            'title'                    => $this->string(128)->notNull(),
            'icon'                     => $this->string(32)->notNull(),
            'settings'                 => $this->text()->notNull(),
            'ordering'                 => $this->integer(11)->null()->defaultValue(null),
            'status'                   => $this->smallInteger(1)->null()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('name', '{{%admin_modules}}', 'name', true);

        $this->batchInsert('{{%admin_modules}}',
            ["module_id", "name", "class", "bootstrapClass", "isFrontend", "controllerNamespace", "viewPath", "isAdmin", "AdminControllerNamespace", "AdminViewPath", "title", "icon", "settings", "ordering", "status"],
            [
                [
                    'module_id'                => '100',
                    'name'                     => 'system',
                    'class'                    => 'app\\modules\\system\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '1',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'System',
                    'icon'                     => '',
                    'settings'                 => '{"BackCallEmail":{"type":"textInput","value":"script@email.ua"},"BackCallSubject":{"type":"textInput","value":"\\u041d\\u043e\\u0432\\u0430\\u044f \\u0437\\u0430\\u044f\\u0432\\u043a\\u0430 \\u0437 \\u0441\\u0430\\u0439\\u0442\\u0430 falconcity.kz"},"BackCallSuccessText":{"type":"textInput","value":"\\u0412\\u0430\\u0448 \\u0437\\u0430\\u043f\\u0440\\u043e\\u0441 \\u043f\\u043e\\u043b\\u0443\\u0447\\u0435\\u043d!<br>\\u0412 \\u0431\\u043b\\u0438\\u0436\\u0430\\u0439\\u0448\\u0435\\u0435 \\u0432\\u0440\\u0435\\u043c\\u044f \\u043d\\u0430\\u0448 \\u043c\\u0435\\u043d\\u0435\\u0434\\u0436\\u0435\\u0440 \\u0441\\u0432\\u044f\\u0436\\u0438\\u0442\\u0441\\u044f \\u0441 \\u0412\\u0430\\u043c\\u0438!"},"SocialInstagramLink":{"type":"textInput","value":"#"},"SocialTwitterLink":{"type":"textInput","value":"#"},"SocialFacebookLink":{"type":"textInput","value":"#"},"FrequentlyAskedQuestionsLink":{"type":"textInput","value":"#"}}',
                    'ordering'                 => '0',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '101',
                    'name'                     => 'menu',
                    'class'                    => 'app\\modules\\menu\\Module',
                    'bootstrapClass'           => 'app\\modules\\menu\\Bootstrap',
                    'isFrontend'               => '1',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Menu',
                    'icon'                     => '',
                    'settings'                 => '[]',
                    'ordering'                 => '1',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '102',
                    'name'                     => 'content',
                    'class'                    => 'app\\modules\\content\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '1',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Content',
                    'icon'                     => '',
                    'settings'                 => '{"show_title":{"type":"checkbox","value":false},"link_titles":{"type":"checkbox","value":false},"show_intro":{"type":"checkbox","value":false},"show_category":{"type":"checkbox","value":false},"link_category":{"type":"checkbox","value":false},"show_parent_category":{"type":"checkbox","value":false},"link_parent_category":{"type":"checkbox","value":false},"show_author":{"type":"checkbox","value":false},"link_author":{"type":"checkbox","value":false},"show_create_date":{"type":"checkbox","value":false},"show_modify_date":{"type":"checkbox","value":false},"show_publish_date":{"type":"checkbox","value":false},"show_hits":{"type":"checkbox","value":false},"categoryThumb":{"type":"checkbox","value":false},"category_items_order":{"type":"select","value":0,"items":{"rdate":"Latest First","date":"Latest Last","rmodified":"Modified First","modified":"Modified Last","alpha":"Alphabetical","ralpha":"Alphabetical Reversed","hits":"Most Hits","rhits":"Least Hits","random":"Random"}}}',
                    'ordering'                 => '2',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '103',
                    'name'                     => 'user',
                    'class'                    => 'app\\modules\\user\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '1',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'User manager',
                    'icon'                     => 'fa fa-user',
                    'settings'                 => '{"title":{"type":"textInput","value":"OAKCMS"}}',
                    'ordering'                 => '3',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '104',
                    'name'                     => 'text',
                    'class'                    => 'app\\modules\\text\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '0',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Text',
                    'icon'                     => '',
                    'settings'                 => '[]',
                    'ordering'                 => '4',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '105',
                    'name'                     => 'shop',
                    'class'                    => 'app\\modules\\shop\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '1',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Shop',
                    'icon'                     => '',
                    'settings'                 => '[]',
                    'ordering'                 => '5',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '106',
                    'name'                     => 'field',
                    'class'                    => 'app\\modules\\field\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '1',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Fields',
                    'icon'                     => '',
                    'settings'                 => '[]',
                    'ordering'                 => '6',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '107',
                    'name'                     => 'filter',
                    'class'                    => 'app\\modules\\filter\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '1',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => '1',
                    'icon'                     => '',
                    'settings'                 => '[]',
                    'ordering'                 => '7',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '108',
                    'name'                     => 'language',
                    'class'                    => 'app\\modules\\language\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '0',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Language',
                    'icon'                     => 'fa fa-flag',
                    'settings'                 => '[]',
                    'ordering'                 => '8',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '109',
                    'name'                     => 'widgets',
                    'class'                    => 'app\\modules\\widgets\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '1',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Widgets',
                    'icon'                     => '',
                    'settings'                 => '{"googlemapseapikey":{"value":"","type":"textInput"},"disable_frontend_style":{"value":"0","type":"checkbox"}}',
                    'ordering'                 => '9',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '110',
                    'name'                     => 'seo',
                    'class'                    => 'app\\modules\\seo\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '0',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Seo',
                    'icon'                     => '',
                    'settings'                 => '{"title":{"type":"textInput","value":"OAKCMS"}}',
                    'ordering'                 => '10',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '111',
                    'name'                     => 'cart',
                    'class'                    => 'app\\modules\\cart\\Module',
                    'bootstrapClass'           => 'app\\modules\\cart\\Bootstrap',
                    'isFrontend'               => '1',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Cart',
                    'icon'                     => '',
                    'settings'                 => '[]',
                    'ordering'                 => '11',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '112',
                    'name'                     => 'gallery',
                    'class'                    => 'app\\modules\\gallery\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '1',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Gallery',
                    'icon'                     => '',
                    'settings'                 => '[]',
                    'ordering'                 => '12',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '113',
                    'name'                     => 'relations',
                    'class'                    => 'app\\modules\\relations\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '1',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Relations',
                    'icon'                     => '',
                    'settings'                 => '[]',
                    'ordering'                 => '13',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '114',
                    'name'                     => 'form_builder',
                    'class'                    => 'app\\modules\\form_builder\\Module',
                    'bootstrapClass'           => 'app\\modules\\form_builder\\Bootstrap',
                    'isFrontend'               => '1',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Form Builder',
                    'icon'                     => '',
                    'settings'                 => '[]',
                    'ordering'                 => '14',
                    'status'                   => '1',
                ],
                [
                    'module_id'                => '115',
                    'name'                     => 'akeebabackup',
                    'class'                    => 'app\\modules\\akeebabackup\\Module',
                    'bootstrapClass'           => '',
                    'isFrontend'               => '0',
                    'controllerNamespace'      => '',
                    'viewPath'                 => '',
                    'isAdmin'                  => '1',
                    'AdminControllerNamespace' => '',
                    'AdminViewPath'            => '',
                    'title'                    => 'Akeeba Backup',
                    'icon'                     => '',
                    'settings'                 => '[]',
                    'ordering'                 => '15',
                    'status'                   => '0',
                ],
            ]
        );

        $this->batchInsert('{{%auth_item}}',
            ["name", "type", "description", "rule_name", "data", "created_at", "updated_at"],
            [
                [
                    'name' => 'administrator',
                    'type' => '1',
                    'description' => 'Administrator',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1460046593',
                    'updated_at' => '1460046593',
                ],
                [
                    'name' => 'manager',
                    'type' => '1',
                    'description' => 'Manager',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1460046592',
                    'updated_at' => '1460046592',
                ],
                [
                    'name' => 'permAdminPanel',
                    'type' => '2',
                    'description' => 'Permission Admin Panel',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1460046593',
                    'updated_at' => '1460046593',
                ],
                [
                    'name' => 'user',
                    'type' => '1',
                    'description' => 'User',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1460046592',
                    'updated_at' => '1460046592',
                ],
            ]
        );

        $this->batchInsert('{{%auth_item_child}}',
            ["parent", "child"],
            [
                [
                    'parent' => 'administrator',
                    'child' => 'manager',
                ],
                [
                    'parent' => 'manager',
                    'child' => 'permAdminPanel',
                ],
                [
                    'parent' => 'manager',
                    'child' => 'user',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%admin_medias}}');
        $this->dropTable('{{%admin_modules}}');
        $this->dropTable('{{%system_db_state}}');
    }
}
