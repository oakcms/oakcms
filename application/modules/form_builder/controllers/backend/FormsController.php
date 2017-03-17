<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\form_builder\controllers\backend;

use app\components\ActiveQuery;
use app\modules\form_builder\models\FormBuilder;
use app\modules\form_builder\models\FormBuilderField;
use app\modules\form_builder\models\search\FormBuilderFieldSearch;
use kartik\builder\BaseForm;
use Yii;
use app\modules\form_builder\models\FormBuilderForms;
use app\modules\form_builder\models\search\FormBuilderFormsSearch;
use app\components\BackendController;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\form\ActiveForm;
use kartik\builder\Form;

/**
 * FormsController implements the CRUD actions for FormBuilderForms model.
 */
class FormsController extends BackendController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new FormBuilderFormsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new FormBuilderForms();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->post('submit-type') == 'continue')
                return $this->redirect(['update', 'id' => $model->id]);
            else
                return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionCreateField($id, $type = false)
    {
        Yii::$app->getView()->applyModalLayout();

        if($type === false) {

            $files = glob(Yii::getAlias('@app/modules/form_builder/views/backend/forms/field/*/field.php'));

            $fields = [];
            foreach ($files as $file) {
                if(is_file($file)) {
                    $file = require $file;
                    $fields[$file['name']] =  $file;
                }
            }
            return $this->render('create-field', ['fields' => $fields, 'form_id' => $id]);
        } else {

            $model = new FormBuilderField();
            $model->form_id = $id;
            $model->type = $type;

            $fieldData = [];
            $modelFormField = null;

            $this->FieldProcess($model, $fieldData, $modelFormField);

            if(!$model->isNewRecord) {
                return $this->redirect(['update-field', 'id' => $model->id]);
            } else {
                return $this->render('field_modal', [
                    'model'          => $model,
                    'fieldData'      => $fieldData,
                    'modelFormField' => $modelFormField,
                ]);
            }
        }
    }

    public function actionUpdate($id)
    {
        $modelForm = $this->findModel($id);

        $searchModelField = new FormBuilderFieldSearch();
        $params = Yii::$app->request->queryParams;
        $params['FormBuilderFieldSearch']['form_id'] = $id;
        $dataProviderField = $searchModelField->search($params);

        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->save()) {
            if (Yii::$app->request->post('submit-type') == 'continue')
                return $this->redirect(['update', 'id' => $modelForm->id]);
            else
                return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model'             => $modelForm,
                'dataProviderField' => $dataProviderField,
            ]);
        }
    }

    public function actionUpdateField($id)
    {
        if (($model = FormBuilderField::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        Yii::$app->getView()->applyModalLayout();

        $fieldData = [];
        $modelFormField = null;

        $this->FieldProcess($model, $fieldData, $modelFormField);

        return $this->render('field_modal', [
            'model'          => $model,
            'fieldData'      => $fieldData,
            'modelFormField' => $modelFormField,
        ]);
    }

    public function actionGetFormHtml($form_id)
    {
        self::getFormHtml($form_id);
    }

    public function actionGetSubmissionVariables($form_id)
    {
        self::getSubmissionVariables($form_id);
    }

    public function actionGetTemplateVariables($form_id)
    {
        self::getTemplateVariables($form_id);
    }

    public function actionGetEmailUser($form_id)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return self::getEmailUser($form_id);
        } else {
            return $this->back();
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteIds()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $id) {
            $this->findModel($id)->delete();
        }
        return $this->back();
    }

    public function actionCloneIds()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        FormBuilderForms::batchCopy($id_arr);
        return $this->back();
    }

    public function actionDeleteFieldsIds()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $id) {
            $this->findModelField($id)->delete();
        }
        return $this->back();
    }

    public function actionPublished()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $id) {
            if (($model = FormBuilderForms::findOne($id)) !== null) {
                $model->status = FormBuilderForms::STATUS_PUBLISHED;
                $model->save();
            }
        }

        return $this->back();
    }

    public function actionUnpublished()
    {
        $ids = Yii::$app->request->get('id');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $id) {
            if (($model = FormBuilderForms::findOne($id)) !== null) {
                $model->status = FormBuilderForms::STATUS_DRAFT;
                $model->save();
            }
        }

        return $this->back();
    }

    public function actionOn($id)
    {
        return $this->changeStatus($id, FormBuilderForms::STATUS_PUBLISHED);
    }

    public function actionOff($id)
    {
        return $this->changeStatus($id, FormBuilderForms::STATUS_DRAFT);
    }

    public function actionSortingFields($id) {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach (\Yii::$app->request->post('sorting') as $order => $id) {
                $model = FormBuilderField::findOne($id);
                if ($model === null) {
                    throw new BadRequestHttpException();
                }
                $model->sort = $order;
                $model->update(false, ['sort']);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
        }
    }

    public static function getFormHtml($form_id)
    {
        $return = '<div id="form_builder_{form:id}">' . PHP_EOL;
        $return .= '<h2>{form:title}</h2>' . PHP_EOL .
            '{error}' . PHP_EOL;
        if (
            ($modelForm = FormBuilderForms::findOne($form_id)) !== null &&
            (
            $modelFields = FormBuilderField::find()
                ->where(['form_id' => $form_id])
                ->orderBy(['sort' => SORT_ASC, 'id' => SORT_DESC])
                ->all()
            ) !== null
        ) {
            foreach ($modelFields as $modelField) {
                $data = json_decode($modelField->data, true);

                $fieldRender = require Yii::getAlias('@app/modules/form_builder/views/backend/forms/field/' . $modelField->type . '/field.php');

                $return .= Yii::$app->getView()->renderFile($fieldRender['render'], [
                    'data' => $data,
                    'slug' => $modelField->slug
                ]);
            }
        }
        $return .= '</div>';
        echo $return;
    }

    public static function getSubmissionVariables($form_id)
    {
        $return = '<div>';
        $return .= '<ul class="list-group">';
        if (
            ($modelForm = FormBuilderForms::findOne($form_id)) !== null &&
            (
            $modelFields = FormBuilderField::find()
                ->where(['form_id' => $form_id])
                ->orderBy(['sort' => SORT_ASC, 'id' => SORT_DESC])
                ->all()
            ) !== null
        ) {
            foreach ($modelFields as $modelField) {
                $return .= '<li class="list-group-item">';
                $return .= '<code>{' . $modelField->slug . ':label}</code><br>';
                $return .= '<code>{' . $modelField->slug . ':value}</code>';
                $return .= '</li>';
            }
        }
        $return .= '</ul>';
        $return .= '</div>';
        echo $return;
    }

    public static function getTemplateVariables($form_id)
    {
        $return = '<div>';
        $return .= '<ul class="list-group">';
        if (
            ($modelForm = FormBuilderForms::findOne($form_id)) !== null &&
            (
            $modelFields = FormBuilderField::find()
                ->where(['form_id' => $form_id])
                ->orderBy(['sort' => SORT_ASC, 'id' => SORT_DESC])
                ->all()
            ) !== null
        ) {
            foreach ($modelFields as $modelField) {
                $return .= '<li class="list-group-item">';
                $return .= '<code>{' . $modelField->slug . ':label}</code><br>';
                $return .= '<code>{' . $modelField->slug . ':body}</code><br>';
                $return .= '<code>{' . $modelField->slug . ':description}</code><br>';
                $return .= '<code>{' . $modelField->slug . ':validation}</code>';
                $return .= '</li>';
            }
        }
        $return .= '</ul>';
        $return .= '</div>';
        echo $return;
    }

    public static function getEmailUser($form_id)
    {
        $return = [];
        if (
            ($modelForm = FormBuilderForms::findOne($form_id)) !== null &&
            (
            $modelFields = FormBuilderField::find()
                ->where(['form_id' => $form_id, 'type' => 'textInput'])
                ->orderBy(['sort' => SORT_ASC, 'id' => SORT_DESC])
                ->all()
            ) !== null
        ) {
            foreach ($modelFields as $modelField) {
                $data = Json::decode($modelField->data);
                if(ArrayHelper::getValue($data, 'type', 'text') == 'email') {
                    $return[] = [
                        'id' => $modelField->id,
                        'text' => $modelField->label,
                    ];
                }
            }
        }
        return $return;
    }

    protected function findModel($id)
    {
        if (($model = FormBuilderForms::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelField($id)
    {
        if (($model = FormBuilderField::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function FieldProcess(&$model, &$fieldData, &$modelFormField) {
        if (is_file($fileL = Yii::getAlias('@app/modules/form_builder/views/backend/forms/field/' . $model->type . '/field.php'))) {
            $fieldData = require $fileL;

            // Перебираємо ключі
            $attributes = [];
            foreach (ArrayHelper::getValue($fieldData, 'attributes', []) as $k => $attribute) {
                $attributes[] = $k;
            }

            $modelFormField = new FormBuilder($attributes);
            foreach (ArrayHelper::getValue($fieldData, 'rules', []) as $rule) {
                $modelFormField->addRule(
                    ArrayHelper::getValue($rule, 0),
                    ArrayHelper::getValue($rule, 1),
                    ArrayHelper::getValue($rule, 2, [])
                );
            }
            $modelFormField->addRule('name', 'match', ['pattern' => '/(^|.*\])([\w\.]+)(\[.*|$)/']);
            $modelFormField->addRule('name', 'unique', [
                'targetClass' => FormBuilderField::className(),
                'targetAttribute' => 'slug',
                'filter' => function ($query) use($model) {
                    /** @var $query ActiveQuery */
                    if($model->isNewRecord) {
                        return $query->andWhere('form_id <> :form_id', ['form_id' => $model->form_id]);
                    } else {
                        return $query->andWhere('form_id = :form_id AND id <> :id', ['form_id' => $model->form_id, 'id' => $model->id]);
                    }
                }
            ]);

            $modelFormField->load(['FormBuilder' => Json::decode($model->data)]);

            if ($modelFormField->load(Yii::$app->request->post()) && $modelFormField->validate()) {
                $saveData = [];
                foreach ($modelFormField->attributes() as $attribute) {
                    $saveData[$attribute] = $modelFormField->{$attribute};
                }
                $model->label = ArrayHelper::getValue($saveData, 'label');
                $model->slug = ArrayHelper::getValue($saveData, 'name');
                $model->data = Json::encode($saveData);
                if ($model->save()) {
                    $this->flash('success', Yii::t('form_builder', '{fieldName} saved.', ['fieldName' => $fieldData['title']]));
                } else {
                    var_dump($model->getErrors());
                    exit;
                }
            }

            if (Json::decode($model->data)) {
                foreach (Json::decode($model->data) as $k=>$item) {
                    $fieldData['attributes'][$k]['options']['value'] = $item;
                }
            }
        }
    }
}
