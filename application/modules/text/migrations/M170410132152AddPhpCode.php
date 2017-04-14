<?php

namespace app\modules\text\migrations;

use app\modules\text\models\Text;
use yii\db\Migration;

class M170410132152AddPhpCode extends Migration
{
    public function safeUp()
    {
        $this->addColumn(Text::tableName(), 'enable_php_code', $this->boolean()->defaultValue(0));
        $this->addColumn(Text::tableName(), 'php_code', $this->text());
    }

    public function safeDown()
    {
        $this->dropColumn(Text::tableName(), 'enable_php_code');
        $this->dropColumn(Text::tableName(), 'php_code');
    }
}
