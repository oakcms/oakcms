<?php

namespace app\modules\system\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "{{%system_back_call}}".
 *
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $comment
 */
class SystemBackCall extends Model
{
    public $name;
    public $email;
    public $phone;
    public $comment;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            [['name', 'email', 'phone'], 'string', 'max' => 255],
            [['comment'], 'string'],
        ];
    }

    public function contact($email, $subject)
    {

        $email = str_replace(' ', '', $email);
        $emails = explode(',', $email);

        $text = "
                <p>
                    You have received a new request for a return call.
                </p>
                <p>
                    Date: <b>".Yii::$app->formatter->asDatetime(time(), "php:d-m-Y H:i:s")."</b><br>
                    Name: <b>$this->name</b><br>
                    Email: <b>$this->email</b><br>
                    Phone: <b>$this->phone</b>
                </p>
                Comment: <br> $this->comment
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
            'name'          => Yii::t('system', 'Name'),
            'email'         => Yii::t('system', 'E-mail'),
            'phone'         => Yii::t('system', 'Phone'),
            'comment'       => Yii::t('system', 'Comment'),
        ];
    }
}
