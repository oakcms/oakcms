<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\modules\seo\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `seo_table`.
 */
class m160513_232135_create_seo_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%seo_items}}', [
            'id'          => $this->primaryKey(),
            'link'        => $this->string(255),
            'title'       => $this->string(255),
            'keywords'    => $this->text(),
            'description' => $this->text(),
            'canonical'   => $this->string(255),
            'status'      => $this->smallInteger()->notNull()->defaultValue(1)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%seo_items}}');
    }
}
