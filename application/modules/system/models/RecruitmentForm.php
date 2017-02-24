<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 24.01.2017
 * Project: kn-group-site
 * File name: Recruitment.php
 */

namespace app\modules\system\models;

use Yii;
use yii\base\Model;

class RecruitmentForm extends Model
{

    public $name;
    public $email;
    public $link;
    public $resume;

    public function rules()
    {
        return [
            [['name', 'email'], 'required'],

            ['email', 'filter', 'filter' => 'trim'],
            //['email', 'email'],

            [['link'], 'string'],
            [['resume'], 'safe'],
        ];
    }

    public function contact($email, $file)
    {

        $email = str_replace(' ', '', $email);
        $emails = explode(',', $email);

        $text = "
                <p>
                    Sent a new resume site KN Group.
                </p>
                <p>
                    Date: <b>".Yii::$app->formatter->asDatetime(time(), "php:d-m-Y H:i:s")."</b><br>
                    Name: <b>$this->name</b><br>
                    Email: <b>$this->email</b><br>
                    Link: <b>$this->link</b><br>
                </p>
            ";

        if ($this->validate()) {

            $mail = Yii::$app->mailer->compose()
                ->setTo($emails)
                ->setFrom(getenv('ROBOT_EMAIL'))
                ->setSubject('Sent a new resume site KN Group')
                ->setHtmlBody($text);

            if($file) {
                $mail->attach($file);
            }

            return $mail->send();
        } else {
            return false;
        }
    }
}
