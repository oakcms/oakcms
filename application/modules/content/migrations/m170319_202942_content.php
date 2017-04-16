<?php

/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */
namespace app\modules\content\migrations;

use yii\db\Migration;

class m170319_202942_content extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';
        $this->createTable('{{%content_articles}}', [
            'id'             => $this->primaryKey(11),
            'create_user_id' => $this->integer(11)->notNull(),
            'update_user_id' => $this->integer(11)->notNull(),
            'published_at'   => $this->integer(11)->notNull(),
            'created_at'     => $this->integer(11)->notNull(),
            'updated_at'     => $this->integer(11)->notNull(),
            'image'          => $this->string(300)->notNull()->defaultValue(''),
            'layout'         => $this->string(255)->notNull(),
            'status'         => $this->integer(11)->notNull()->defaultValue(0),
            'comment_status' => $this->integer(11)->notNull()->defaultValue(1),
            'create_user_ip' => $this->string(20)->notNull(),
            'access_type'    => $this->integer(11)->notNull()->defaultValue(1),
            'category_id'    => $this->integer(11)->null()->defaultValue(null),
            'main_image'     => $this->integer(11)->notNull()->defaultValue(1),
            'hits'           => $this->integer(11)->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('create_user_id', '{{%content_articles}}', 'create_user_id', false);
        $this->createIndex('update_user_id', '{{%content_articles}}', 'update_user_id', false);
        $this->createIndex('status', '{{%content_articles}}', 'status', false);
        $this->createIndex('access_type', '{{%content_articles}}', 'access_type', false);
        $this->createIndex('comment_status', '{{%content_articles}}', 'comment_status', false);
        $this->createIndex('publish_date', '{{%content_articles}}', 'updated_at', false);
        $this->createIndex('category_id', '{{%content_articles}}', 'category_id', false);

        $this->createTable('{{%content_articles_lang}}', [
            'id'                  => $this->primaryKey(11),
            'content_articles_id' => $this->integer(11)->notNull(),
            'slug'                => $this->string(150)->notNull(),
            'title'               => $this->string(255)->notNull(),
            'description'         => $this->text()->notNull(),
            'content'             => $this->text()->notNull(),
            'link'                => $this->string(255)->notNull()->defaultValue(''),
            'meta_title'          => $this->string(255)->notNull(),
            'meta_keywords'       => $this->string(255)->notNull(),
            'meta_description'    => $this->string(255)->notNull(),
            'settings'            => $this->text()->notNull(),
            'language'            => $this->string(10)->notNull(),
        ], $tableOptions);

        $this->createIndex('slug', '{{%content_articles_lang}}', 'slug', false);

        $this->createTable('{{%content_articles_medias}}', [
            'id'                  => $this->primaryKey(11)->unsigned(),
            'content_articles_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0),
            'media_id'            => $this->integer(11)->unsigned()->notNull()->defaultValue(0),
            'ordering'            => $this->integer(3)->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('i_virtuemart_product_id', '{{%content_articles_medias}}', 'content_articles_id,media_id', true);
        $this->createIndex('i_ordering', '{{%content_articles_medias}}', 'ordering', false);
        $this->createTable('{{%content_category}}', [
            'id'            => $this->primaryKey(11)->comment('Unique tree node identifier'),
            'layout'        => $this->string(255)->notNull(),
            'status'        => $this->smallInteger(6)->notNull(),
            'created_at'    => $this->integer(11)->notNull(),
            'updated_at'    => $this->integer(11)->notNull(),
            'tree'          => $this->integer(11)->null()->defaultValue(null)->comment('Tree root identifier'),
            'lft'           => $this->integer(11)->notNull()->comment('Nested set left property'),
            'rgt'           => $this->integer(11)->notNull()->comment('Nested set right property'),
            'depth'         => $this->smallInteger(5)->notNull()->comment('Nested set level / depth'),
            'icon'          => $this->string(255)->null()->defaultValue(null)->comment('The icon to use for the node'),
            'icon_type'     => $this->smallInteger(1)->notNull()->defaultValue(1)->comment('Icon Type: 1 = CSS Class, 2 = Raw Markup'),
            'active'        => $this->smallInteger(1)->notNull()->defaultValue(1)->comment('Whether the node is active (will be set to false on deletion)'),
            'selected'      => $this->smallInteger(1)->notNull()->defaultValue(0)->comment('Whether the node is selected/checked by default'),
            'disabled'      => $this->smallInteger(1)->notNull()->defaultValue(0)->comment('Whether the node is enabled'),
            'readonly'      => $this->smallInteger(1)->notNull()->defaultValue(0)->comment('Whether the node is read only (unlike disabled - will allow toolbar actions)'),
            'visible'       => $this->smallInteger(1)->notNull()->defaultValue(1)->comment('Whether the node is visible'),
            'collapsed'     => $this->smallInteger(1)->notNull()->defaultValue(0)->comment('Whether the node is collapsed by default'),
            'movable_u'     => $this->smallInteger(1)->notNull()->defaultValue(1)->comment('Whether the node is movable one position up'),
            'movable_d'     => $this->smallInteger(1)->notNull()->defaultValue(1)->comment('Whether the node is movable one position down'),
            'movable_l'     => $this->smallInteger(1)->notNull()->defaultValue(1)->comment('Whether the node is movable to the left (from sibling to parent)'),
            'movable_r'     => $this->smallInteger(1)->notNull()->defaultValue(1)->comment('Whether the node is movable to the right (from sibling to child)'),
            'removable'     => $this->smallInteger(1)->notNull()->defaultValue(1)->comment('Whether the node is removable (any children below will be moved as siblings before deletion)'),
            'removable_all' => $this->smallInteger(1)->notNull()->defaultValue(0)->comment('Whether the node is removable along with descendants'),
            'order'         => $this->integer(11)->notNull(),
            'parent'        => $this->integer(11)->notNull(),
            'children'      => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createIndex('tbl_tree_NK1', '{{%content_category}}', 'tree', false);
        $this->createIndex('tbl_tree_NK2', '{{%content_category}}', 'lft', false);
        $this->createIndex('tbl_tree_NK3', '{{%content_category}}', 'rgt', false);
        $this->createIndex('tbl_tree_NK4', '{{%content_category}}', 'depth', false);
        $this->createIndex('tbl_tree_NK5', '{{%content_category}}', 'active', false);

        $this->createTable('{{%content_category_lang}}', [
            'id'                  => $this->primaryKey(11),
            'content_category_id' => $this->integer(11)->notNull(),
            'slug'                => $this->string(150)->notNull(),
            'title'               => $this->string(255)->notNull(),
            'content'             => $this->text()->notNull(),
            'meta_title'          => $this->string(255)->notNull(),
            'meta_keywords'       => $this->string(255)->notNull(),
            'meta_description'    => $this->string(255)->notNull(),
            'settings'            => $this->text()->notNull(),
            'language'            => $this->string(10)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%content_pages}}', [
            'id'               => $this->primaryKey(11),
            'lft'              => $this->integer(11)->notNull(),
            'rgt'              => $this->integer(11)->notNull(),
            'level'            => $this->integer(11)->notNull(),
            'parent_id'        => $this->integer(11)->notNull(),
            'layout'           => $this->string(255)->notNull(),
            'background_image' => $this->string(255)->notNull(),
            'icon_image'       => $this->text()->notNull(),
            'status'           => $this->integer(11)->notNull(),
            'created_at'       => $this->integer(11)->notNull(),
            'updated_at'       => $this->integer(11)->notNull(),
            'ordering'         => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%content_pages_lang}}', [
            'id'               => $this->primaryKey(11),
            'content_pages_id' => $this->integer(11)->notNull(),
            'title'            => $this->string(255)->notNull(),
            'subtitle'         => $this->string(255)->notNull()->defaultValue(''),
            'title_h1'         => $this->string(255)->notNull()->defaultValue(''),
            'slug'             => $this->string(255)->notNull(),
            'description'      => $this->text()->notNull(),
            'content'          => $this->text()->notNull(),
            'meta_title'       => $this->string(255)->notNull(),
            'meta_keywords'    => $this->string(255)->notNull(),
            'meta_description' => $this->string(255)->notNull(),
            'settings'         => $this->text()->notNull(),
            'language'         => $this->string(10)->notNull(),
        ], $tableOptions);

        $this->createIndex('content_pages_id', '{{%content_pages_lang}}', 'content_pages_id', false);

        $this->createTable('{{%content_tag_assn}}', [
            'content_id'      => $this->integer(11)->notNull(),
            'content_tags_id' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%content_tags}}', [
            'id'        => $this->primaryKey(11),
            'frequency' => $this->integer(10)->notNull(),
            'name'      => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->createIndex('name', '{{%content_tags}}', 'name', false);

        $this->batchInsert('{{%content_pages}}',
            ["id", "lft", "rgt", "level", "parent_id", "layout", "background_image", "icon_image", "status", "created_at", "updated_at", "ordering"],
            [
                [
                    'id' => '1',
                    'lft' => '1',
                    'rgt' => '12',
                    'level' => '0',
                    'parent_id' => '0',
                    'layout' => '',
                    'background_image' => '',
                    'icon_image' => '',
                    'status' => '1',
                    'created_at' => '1476250048',
                    'updated_at' => '1483477411',
                    'ordering' => '1',
                ],
                [
                    'id' => '2',
                    'lft' => '2',
                    'rgt' => '3',
                    'level' => '1',
                    'parent_id' => '1',
                    'layout' => 'default',
                    'background_image' => '',
                    'icon_image' => '',
                    'status' => '1',
                    'created_at' => '1492344072',
                    'updated_at' => '1492344072',
                    'ordering' => '1',
                ],
            ]
        );

        $this->batchInsert('{{%content_pages_lang}}',
            ["id", "content_pages_id", "title", "subtitle", "title_h1", "slug", "description", "content", "meta_title", "meta_keywords", "meta_description", "settings", "language"],
            [
                [
                    'id' => '2',
                    'content_pages_id' => '2',
                    'title' => 'Home',
                    'subtitle' => '',
                    'title_h1' => '',
                    'slug' => 'home',
                    'description' => '',
                    'content' => '<p>Some home page</p>',
                    'meta_title' => '',
                    'meta_keywords' => '',
                    'meta_description' => '',
                    'settings' => '[]',
                    'language' => 'ru-ru',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%content_articles}}');
        $this->dropTable('{{%content_articles_lang}}');
        $this->dropTable('{{%content_articles_medias}}');
        $this->dropTable('{{%content_category}}');
        $this->dropTable('{{%content_category_lang}}');
        $this->dropTable('{{%content_pages}}');
        $this->dropTable('{{%content_pages_lang}}');
        $this->dropTable('{{%content_tag_assn}}');
        $this->dropTable('{{%content_tags}}');
    }
}
