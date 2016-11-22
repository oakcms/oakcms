<?php
use app\components\CategoryModel;
use yii\helpers\Url;
use app\modules\admin\widgets\Button;

\yii\bootstrap\BootstrapPluginAsset::register($this);

$this->title = Yii::$app->getModule('admin')->activeModules[$this->context->module->id]->title;

$baseUrl = '/admin/'.$this->context->moduleName;
$this->params['actions_buttons'] = [
    [
        'tagName' => 'a',
        'label' => Yii::t('admin', 'Create'),
        'options' => [
            'href' => Url::to(['create'])
        ],
        'icon' => 'fa fa-plus',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
    ]
];
?>

<?php if(sizeof($cats) > 0) : ?>
    <table class="table table-hover">
        <tbody>
            <?php foreach($cats as $cat) : ?>
                <tr>
                    <td width="50"><?= $cat->id ?></td>
                    <td style="padding-left:  <?= $cat->depth * 20 ?>px;">
                        <?php if(count($cat->children)) : ?>
                            <i class="caret"></i>
                        <?php endif; ?>

                        <a href="<?= Url::to([$baseUrl . $this->context->viewRoute, 'id' => $cat->id]) ?>" <?= ($cat->status == CategoryModel::STATUS_OFF ? 'class="smooth"' : '') ?>>
                            <?= $cat->title ?>
                        </a>
                    </td>
                    <td width="120" class="text-right">
                        <div class="dropdown actions">
                            <i id="dropdownMenu<?= $cat->id ?>" data-toggle="dropdown" aria-expanded="true" title="<?= Yii::t('content', 'Actions') ?>" class="glyphicon glyphicon-menu-hamburger"></i>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu<?= $cat->id ?>">
                                <li>
                                    <a href="<?= Url::to([$baseUrl.'/category/update', 'id' => $cat->id, 'language' => \Yii::$app->session->get('_languages')['url']]) ?>">
                                        <i class="glyphicon glyphicon-pencil font-12"></i> <?= Yii::t('content', 'Edit') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= Url::to([$baseUrl.'/category/create', 'parent' => $cat->id]) ?>"><i class="glyphicon glyphicon-plus font-12"></i> <?= Yii::t('easyii', 'Add subcategory') ?></a></li>
                                <li role="presentation" class="divider"></li>
                                <li><a href="<?= Url::to([$baseUrl.'/category/up', 'id' => $cat->id]) ?>"><i class="glyphicon glyphicon-arrow-up font-12"></i> <?= Yii::t('easyii', 'Move up') ?></a></li>
                                <li><a href="<?= Url::to([$baseUrl.'/category/down', 'id' => $cat->id]) ?>"><i class="glyphicon glyphicon-arrow-down font-12"></i> <?= Yii::t('easyii', 'Move down') ?></a></li>
                                <li role="presentation" class="divider"></li>
                                <?php if($cat->status == CategoryModel::STATUS_ON) :?>
                                    <li><a href="<?= Url::to([$baseUrl.'/category/off', 'id' => $cat->id]) ?>" title="<?= Yii::t('easyii', 'Turn Off') ?>'"><i class="glyphicon glyphicon-eye-close font-12"></i> <?= Yii::t('easyii', 'Turn Off') ?></a></li>
                                <?php else : ?>
                                    <li><a href="<?= Url::to([$baseUrl.'/category/on', 'id' => $cat->id]) ?>" title="<?= Yii::t('easyii', 'Turn On') ?>"><i class="glyphicon glyphicon-eye-open font-12"></i> <?= Yii::t('easyii', 'Turn On') ?></a></li>
                                <?php endif; ?>
                                <li><a href="<?= Url::to([$baseUrl.'/category/delete', 'id' => $cat->id]) ?>" class="confirm-delete" data-reload="1" title="<?= Yii::t('easyii', 'Delete item') ?>"><i class="glyphicon glyphicon-remove font-12"></i> <?= Yii::t('easyii', 'Delete') ?></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
<?php else : ?>
    <p><?= Yii::t('admin', 'No records found') ?></p>
<?php endif; ?>
