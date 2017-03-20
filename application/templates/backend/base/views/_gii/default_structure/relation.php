<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/** @var $migrationName string the new migration class name
 * @var array                                  $tableRelations
 * @var insolita\migrik\gii\StructureGenerator $generator
 * @var array                                  $fkProps
 */

$namespace = str_replace(['@', '/'], ['', '\\'], $generator->migrationPath);
echo "<?php\n";
?>
namespace <?= $namespace; ?>;

use yii\db\Schema;
use yii\db\Migration;

class <?= $migrationName ?> extends Migration
{

public function init()
{
$this->db = '<?= $generator->db ?>';
parent::init();
}

public function safeUp()
{
<?php if (!empty($tableRelations) && is_array($tableRelations)) : ?>
    <?php foreach ($tableRelations as $table) : ?>
        <?php foreach ($table['fKeys'] as $i => $rel) : ?>
            $this->addForeignKey('fk_<?= $table['tableName'] ?>_<?= $rel['pk'] ?>','<?= $table['tableAlias'] ?>','<?= $rel['pk'] ?>','<?= $rel['ftable'] ?>',
            '<?= $rel['fk'] ?>','<?= $fkProps['onDelete'] ?>','<?= $fkProps['onUpdate'] ?>');
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif ?>
}

public function safeDown()
{
<?php if (!empty($tableRelations) && is_array($tableRelations)) : ?>
    <?php foreach ($tableRelations as $table) : ?>
        <?php foreach ($table['fKeys'] as $i => $rel) : ?>
            $this->dropForeignKey('fk_<?= $table['tableName'] ?>_<?= $rel['pk'] ?>', '<?= $table['tableAlias'] ?>');
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif ?>
}
}
