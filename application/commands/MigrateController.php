<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\commands;

use Yii;

class MigrateController extends \yii\console\controllers\MigrateController
{
    /** @inheritdoc */
    public $templateFile = '@app/views/migration.php';

    /** @inheritdoc */
    public $migrationTable = 'migration';

    /** @inheritdoc */
    protected $namespace = 'app\migrations';

    /**
     * @var array paths to migrations like [path => migrationName]
     */
    public $migrationFiles = [];


    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if ($action->id !== 'create' && is_object($this->db->schemaCache)) {
            $this->db->schemaCache->flush();
        }

        $this->migrationNamespaces['yii-log'] = 'yii\log\migrations';
        $this->migrationNamespaces['yii-rbac'] = 'yii\rbac\migrations';

        $this->attachModuleMigrations();
        return true;
    }

    /**
     * Migrates current module up.
     *
     * @param string         $module module name.
     * @param string|integer $limit  migrations limit.
     */
    public function actionModuleUp($module, $limit = 'all')
    {
        $this->attachModuleMigrations($module);
        parent::actionUp($limit);
    }
    /**
     * Migrates current module down.
     *
     * @param string         $module module name.
     * @param string|integer $limit  migrations limit.
     */
    public function actionModuleDown($module, $limit = 'all')
    {
        $this->attachModuleMigrations($module);
        parent::actionDown($limit);
    }

    /**
     * @inheritdoc
     */
    protected function createMigration($class)
    {
        $class = trim($class, '\\');

        if (strpos($class, '\\') === false) {
            $class = $this->namespace . '\\' . $class;
        } elseif (strpos($class, 'yii\\') !== false) {
            $path = $this->getNamespacePath($class);
            $file = $path . '.php';

            $class = explode(DIRECTORY_SEPARATOR, $path);
            $class = array_pop($class);

            require_once($file);
        }

        return new $class(['db' => $this->db]);
    }

    /**
     * Returns the file path matching the give namespace.
     * @param string $namespace namespace.
     * @return string file path.
     * @since 2.0.10
     */
    private function getNamespacePath($namespace)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, Yii::getAlias('@' . str_replace('\\', '/', $namespace)));
    }

    /**
     * @param $module string|null
     * creates $migrationNamespaces attribute from module base paths
     */
    protected function attachModuleMigrations($module = null)
    {
        $migrationNamespaces = [];
        foreach (Yii::$app->modules as $name => $config) {
            $namespace = new \ReflectionClass(get_class(Yii::$app->getModule($name)));
            $migrationNamespaces[$name] = $namespace->getNamespaceName() . '\\' . 'migrations';
        }

        if($module === null) {
            $this->migrationNamespaces = $migrationNamespaces;
        } elseif (isset($this->migrationNamespaces[$module])) {
            $this->migrationNamespaces = [$module => $this->migrationNamespaces[$module]];
        } else {
            $this->migrationNamespaces = [];
        }
    }
}
