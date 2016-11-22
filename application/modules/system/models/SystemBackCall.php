<?php

namespace app\modules\system\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%system_back_call}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $comment
 * @property integer $created_at
 * @property integer $status
 */
class SystemBackCall extends \yii\db\ActiveRecord
{
    const ST_NEW        = 1;
    const ST_PROCESSED  = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_back_call}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['name', 'email', 'phone'], 'required'],
            [['name', 'email'], 'required'],
            [['created_at', 'status'], 'integer'],
            [['name', 'email', 'phone'], 'string', 'max' => 255],
            [['comment'], 'string'],
        ];
    }

    public static function getStatus($status = false) {
        $statuses = [
            self::ST_NEW => Yii::t('common', 'New'),
            self::ST_PROCESSED => Yii::t('common', 'Processed'),
        ];
        if($status) {
            return $statuses[$status];
        }
        return $statuses;
    }

    public function contact($email, $subject)
    {

        $email = str_replace(' ', '', $email);
        $emails = explode(',', $email);

        $text = "
                <p>
                    Вы получили новый запрос на обратный звонок.
                </p>
                <p>
                    Дата: <b>".Yii::$app->formatter->asDatetime($this->created_at, "php:d-m-Y H:i:s")."</b><br>
                    Имя: <b>$this->name</b><br>
                    Email: <b>$this->email</b><br>
                    Телефон: <b>$this->phone</b>
                </p>
                Комментарий: <br> $this->comment
            ";

        if ($this->validate()) {
            return Yii::$app->mailer->compose()
                ->setTo($emails)
                ->setFrom(getenv('ROBOT_EMAIL'))
                ->setSubject($subject)
                ->setHtmlBody($text)
                ->send();
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => Yii::t('system', 'ID'),
            'name'          => Yii::t('system', 'Name'),
            'email'         => Yii::t('system', 'E-mail'),
            'phone'         => Yii::t('system', 'Phone'),
            'comment'       => Yii::t('system', 'Comment'),
            'created_at'    => Yii::t('system', 'Created At'),
            'status'        => Yii::t('system', 'Status'),
        ];
    }
}
