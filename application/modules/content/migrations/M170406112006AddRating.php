<?php

namespace app\modules\content\migrations;

use yii\db\Migration;

class M170406112006AddRating extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%content_articles}}', 'rating', $this->float()->defaultValue(0));
        $this->addColumn('{{%content_articles}}', 'rating_sum', $this->float()->defaultValue(0));
        $this->addColumn('{{%content_articles}}', 'rating_votes', $this->integer(11)->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%content_articles}}', 'rating');
        $this->dropColumn('{{%content_articles}}', 'rating_sum');
        $this->dropColumn('{{%content_articles}}', 'rating_votes');
    }
}
