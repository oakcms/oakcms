<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\modules\user\migrations;

use app\modules\admin\rbac\Rbac;
use app\modules\user\models\backend\User;
use yii\db\Migration;

class m170319_203350_user_setup extends Migration
{
    public function up()
    {
        // Creates the default admin user
        $adminUser = new User();
        $adminUser->setScenario(User::SCENARIO_ADMIN_CREATE_INIT);
        $adminUser->status = User::STATUS_ACTIVE;
        $adminUser->role = Rbac::PERMISSION_ADMINISTRATOR;
        echo 'Please type the admin user info: ' . PHP_EOL;
        $this->readStdinUser('Email (e.g. admin@domain.com)', $adminUser, 'email');
        $this->readStdinUser('Type Username', $adminUser, 'username', 'admin');
        $this->readStdinUser('Type Password', $adminUser, 'newPassword', 'admin');
        if ($adminUser->save()) {
            $adminUser->afterSingUp();
        } else {
            throw new \yii\console\Exception('Error when creating admin user.');
        }

        echo 'User created successfully.' . PHP_EOL;
    }

    public function down()
    {

    }

    /**
     * @param string          $prompt
     * @param \yii\base\Model $model
     * @param string          $field
     * @param string          $default
     *
     * @return string
     */
    private function readStdinUser($prompt, $model, $field, $default = '')
    {
        while (!isset($input) || !$model->validate([$field])) {
            echo $prompt . (($default) ? " [$default]" : '') . ': ';
            $input = (trim(fgets(STDIN)));
            if (empty($input) && !empty($default)) {
                $input = $default;
            }
            $model->$field = $input;
        }

        return $input;
    }
}
