<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\seo\migrations;

use app\components\Migration;

/**
 * Handles the creation for table `seo_table`.
 */
class m160513_232135_create_seo_table extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        } else {
            $tableOptions = null;
        }

        $this->createTable('{{%seo_items}}', [
            'id'          => $this->primaryKey(),
            'link'        => $this->string(255),
            'title'       => $this->string(255),
            'keywords'    => $this->text(),
            'description' => $this->text(),
            'canonical'   => $this->string(255),
            'status'      => $this->smallInteger()->notNull()->defaultValue(1)
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%seo_items}}');
    }
}
