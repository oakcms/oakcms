<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\commands;

use Yii;
use yii\console\Application;
use yii\console\Exception;
use yii\helpers\Console;

class MigrateController extends \yii\console\controllers\MigrateController
{
    /** @inheritdoc */
    public $templateFile = '@app/views/migration.php';

    /** @inheritdoc */
    public $migrationTable = 'migration';

    /** @inheritdoc */
    protected $namespace = 'app\migrations';

    /**
     * @var array module base paths
     */
    public $allMigrationPaths = [];
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
        $this->allMigrationPaths['app'] = $this->migrationPath;
        $this->attachModuleMigrations();
        $this->setMigrationFiles();

        return true;
    }

    /** @inheritdoc */
    public function actionCreate($name)
    {
        if (!preg_match('/^\w+$/', $name)) {
            throw new Exception("The migration name should contain letters, digits and/or underscore characters only.");
        }

        $className = 'migrate' . gmdate('ymd_His') . '_' . $name;

        // По сравнению со стандартной логикой поменялись только следующие три строчки. Остальное не тронуто.
        $namespace = $this->namespace;
        $fullClassName = "$namespace\\{$className}";
        $file = $this->getFileOfClass($fullClassName);

        if ($this->confirm("Create new migration '$file'?")) {
            $content = $this->renderFile(\Yii::getAlias($this->templateFile), [
                'className' => $className,
                'namespace' => $namespace,
            ]);
            file_put_contents($file, $content);
            $this->stdout("New migration created successfully.\n", Console::FG_GREEN);
        }
    }

    /**
     * Формирует имя файла с миграцией на основе полного имени класса.
     *
     * @param string $className Полное имя класса.
     *
     * @return string Путь к файлу.
     */
    protected function getFileOfClass($className)
    {
        $alias = '@' . str_replace('\\', '/', $className);

        return \Yii::getAlias($alias) . '.php';
    }

    /**
     * @inheritdoc
     */
    protected function getNewMigrations()
    {
        $result = [];
        foreach ($this->allMigrationPaths as $path) {
            $this->migrationPath = $path;
            if (!file_exists($path)) {
                continue;
            }
            $result = array_merge($result, parent::getNewMigrations());
        }
        $this->migrationPath = $this->allMigrationPaths['app'];
        sort($result);

        return $result;
    }

    /**
     * gets path to migration file
     *
     * @param string      $name migration name
     * @param bool|string $path module migrations base path
     *
     * @return string path to migration file
     */
    protected function getMigrationFile($name, $path = false)
    {
        $path = $path ? $path : $this->migrationPath;

        return $path . DIRECTORY_SEPARATOR . $name . '.php';
    }

    /**
     * @inheritdoc
     */
    protected function createMigration($class)
    {
        if (!$file = array_search($class, $this->migrationFiles)) {
            return false;
        }
        require_once($file);

        return new $class(['db' => $this->db]);
    }

    /**
     * creates $allMigrationPaths attribute from module base paths
     */
    protected function attachModuleMigrations()
    {
        foreach (Yii::$app->modules as $name => $config) {
            $basePath = Yii::$app->getModule($name)->basePath;
            $path = $basePath . DIRECTORY_SEPARATOR . 'migrations';
            if ($this->allMigrationPaths['app'] == $path) {
                continue;
            }
            if (file_exists($path) && !is_file($path)) {
                $this->allMigrationPaths[$name] = $path;
            }
        }
    }

    /**
     * Creates $migrationFiles array
     * @return array list of migrations like [path=>migrationName]
     */
    protected function setMigrationFiles()
    {
        $result = [];
        foreach ($this->allMigrationPaths as $path) {
            if (!file_exists($path) || is_file($path)) {
                continue;
            }
            $handle = opendir($path);
            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $filePath = $path . DIRECTORY_SEPARATOR . $file;
                if (preg_match('/^(m(\d{6}_\d{6})_.*?)\.php$/', $file, $matches) && is_file($filePath)) {
                    $result[$filePath] = $matches[1];
                }
            }
            closedir($handle);
        }

        return $this->migrationFiles = $result;
    }

    /**
     * Migrates current module up.
     *
     * @param string         $module module name.
     * @param string|integer $limit  migrations limit.
     */
    public function actionModuleUp($module, $limit = 'all')
    {
        $this->setModuleMigrationPaths($module);
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
        $this->setModuleMigrationPaths($module);
        parent::actionDown($limit);
    }

    /**
     * Sets modules array - leaves only module migrations.
     *
     * @param string $module module name.
     */
    protected function setModuleMigrationPaths($module)
    {
        $paths = ['app' => Yii::getAlias('@app/runtime/tmp')];
        if (isset($this->allMigrationPaths[$module])) {
            $paths[$module] = $this->allMigrationPaths[$module];
        }
        $this->allMigrationPaths = $paths;
        $this->setMigrationFiles();
    }

    /**
     * @inheritdoc
     */
    protected function getMigrationHistory($limit)
    {
        $history = parent::getMigrationHistory($limit);
        foreach ($history as $name => $time) {
            if (!$this->migrationExists($name)) {
                unset($history[$name]);
            }
        }

        return $history;
    }

    /**
     * Checks if a migration exists
     *
     * @param string $name the name of the migration to check for.
     *
     * @return bool
     */
    protected function migrationExists($name)
    {
        return in_array($name, $this->migrationFiles);
    }

    public function stderr($string)
    {
        return Yii::$app instanceof Application ? parent::stderr($string) : true;
    }

    public function stdout($string)
    {
        return Yii::$app instanceof Application ? parent::stdout($string) : true;
    }
}
