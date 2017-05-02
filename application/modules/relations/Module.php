<?php
namespace app\modules\relations;

use app\modules\admin\rbac\Rbac;

class Module extends \app\components\module\Module
{
    public $adminRoles = [Rbac::PERMISSION_ADMIN_PANEL];
    public $fields = [];
    public $settings = [];

    public function init()
    {
        parent::init();
    }
}
