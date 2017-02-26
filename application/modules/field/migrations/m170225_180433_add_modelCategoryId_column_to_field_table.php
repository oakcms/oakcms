<?php

use yii\db\Migration;

/**
 * Handles adding modelCategoryId to table `field`.
 */
class m170225_180433_add_modelCategoryId_column_to_field_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('field', 'model_category_id', $this->text());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('field', 'model_category_id');
    }
}
