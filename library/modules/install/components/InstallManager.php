<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\install\components;

use yii\base\Component;
use usni\UsniAdaptor;
use usni\library\components\UiHtml;
use usni\library\utils\DatabaseUtil;
use yii\db\Connection;
use usni\library\modules\users\utils\UserUtil;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\utils\ConfigurationUtil;
use usni\library\utils\FileUtil;
/**
 * InstallManager class file.
 *
 * @package usni\library\modules\install\components
 */
class InstallManager extends Component
{
    /**
     * Php Memory Limit in MB
     */
    const PHP_MEMORY_LIMIT = 64;
    /**
     * File Upload Size in MB
     */
    const FILE_UPLOAD_SIZE = 2;
    /**
     * PHP Post Size in MB
     */
    const PHP_POST_SIZE = 2;
    
    /**
     * Key to install the app
     */
    const INSTALL_KEY = '4QKLfI-BWpDWOf258Otj3AalM9fE2lZD';
    
    /**
     * Execution time limit for installation.
     * @var int
     */
    public $executionTimeLimit = 1800;
    /**
     * Output buffer streamer.
     * @var OutputBufferStreamer
     */
    public $obStreamer;
    /**
     * Model containing the site configuration.
     * @var SettingsForm
     */
    public $configModel;
    /**
     * Installed apache version.
     * @var string
     */
    public static $installedApacheVersion;
    /**
     * Configuration file path.
     * @var string
     */
    public $targetConfigFilePath;
    /**
     * Test Configuration file path.
     * @var string
     */
    public $targetTestConfigFilePath;
    /**
     * Instance Configuration file path.
     * @var string
     */
    public $targetInstanceConfigFilePath;
    
    /**
     * Show buffer message
     * @var boolean 
     */
    public $showBufferMessage = true;

    /**
     * Run system installation.
     * @param SettingsForm $model
     * @return void
     */
    public function runInstallation($model, $obStreamer, $configFile = null, $configTestFile = null)
    {
        UsniAdaptor::app()->cache->flush();
        $this->configModel  = $model;
        $this->obStreamer   = $obStreamer;
        $this->addMessage(UsniAdaptor::t('install', 'Begin Installation'));
        if($configFile != null)
        {
            $configFilePath             = UsniAdaptor::getAlias('@common/config');
            $this->targetConfigFilePath = $configFilePath . '/' . $configFile;
            if($configTestFile != null)
            {
                $this->targetTestConfigFilePath = $configFilePath . '/' . $configTestFile;
            }
            else
            {
                $this->targetTestConfigFilePath = $configFilePath . '/instancetest.php';
            }
            $this->targetInstanceConfigFilePath = $configFilePath . '/instanceConfig.php';
            //Sets time limit
            @set_time_limit($this->executionTimeLimit);
            $this->addMessage(UsniAdaptor::t('install', 'Set time limit for execution to') . ' ' . $this->executionTimeLimit);
            $this->setDbComponent();
            $this->installDatabase();
            $this->saveSettingsInDatabase();
            $this->loadData();
            ConfigurationUtil::insertOrUpdateConfiguration('application', 'installTime', date('Y-m-d H:i:s'));
            $this->backupDatabase();
            $this->writeConfigFile();
            $this->addMessage(UsniAdaptor::t('install', 'Installation completed sucessfully'));
            $this->addProgressMessage('100');
            $installSettingsFile = FileUtil::normalizePath(UsniAdaptor::app()->getRuntimePath() . DS . 'install' . DS .  'settingsdata.bin');
            @unlink($installSettingsFile);
            $template           = UiHtml::script("$('#progress-container').hide();$('#final-overview').removeClass('hide').addClass('show');");
            $this->obStreamer->setTemplate($template);
            $this->addMessage('');
        }
        else
        {
            $template           = UiHtml::script("$('#progress-container').hide();
                                                  $('#install-errors').removeClass('hide').addClass('show');
                                                  $('#error-messages').html('{message}')");
            $this->obStreamer->setTemplate($template);
            $message = UsniAdaptor::t('install', 'Configuration file is missing');
            $this->addMessage($message);
            UsniAdaptor::app()->end();
        }
    }

    /**
     * Install Database.
     * @return void
     * @throws PDOException
     */
    public function installDatabase()
    {
        $transaction    = UsniAdaptor::db()->beginTransaction();
        try
        {
            $basePath = APPLICATION_PATH . '/data';
            //Build database
            $this->addMessage(UsniAdaptor::t('install', 'Start building database'));
            DatabaseUtil::removeTablesFromDatabase();
            $this->addProgressMessage('20');
            $this->addMessage(UsniAdaptor::t('install', 'Remove tables from database'));
            $this->buildTables();
            //Check if instance sql exists or not.
            $databaseInstanceFile = $basePath . '/instancedb.sql';
            if(file_exists($databaseInstanceFile))
            {
                $sql  = file_get_contents($databaseInstanceFile);
                if(!empty($sql))
                {
                    UsniAdaptor::db()->createCommand($sql)->execute();
                }
            }
            $transaction->commit();
            UsniAdaptor::db()->getSchema()->refresh();
            $this->addMessage(UsniAdaptor::t('install', 'Database creation successfull'));
            $this->addProgressMessage('50');

        }
        catch (\yii\db\Exception $e)
        {
            $template           = UiHtml::script("$('#progress-container').hide();
                                                  $('#install-errors').removeClass('hide').addClass('show');
                                                  $('#error-messages').html('{message}')");
            $this->obStreamer->setTemplate($template);
            $message = UsniAdaptor::t('install', 'Creation of database fails with error {error}', array('error' =>  $e->getMessage()));
            $this->addMessage($message);
            $transaction->rollback();
            UsniAdaptor::app()->end();
        }
    }

    /**
     * Sets db component.
     * @return void.
     */
    public function setDbComponent()
    {
        try
        {
            $model = $this->configModel;
            $dsn = 'mysql:host=' . $model->dbHost . ';dbname=' . $model->dbName . ';port=' . $model->dbPort . ';';
            $connection = new Connection();
            $connection->dsn = $dsn;
            $connection->username = $model->dbUsername;
            $connection->password = $model->dbPassword;
            $connection->charset = 'utf8';
            $connection->tablePrefix = 'tbl_';
            $connection->emulatePrepare = true;
            UsniAdaptor::app()->set('db', $connection);
        }
        catch (\yii\db\Exception $e)
        {
            $template           = UiHtml::script("$('#progress-container').hide();
                                                  $('#install-errors').removeClass('hide').addClass('show');
                                                  $('#error-messages').html('{message}')");
            $this->obStreamer->setTemplate($template);
            $message = UsniAdaptor::t('install', 'Database connection fails with error code {error}',
                                                    array('{error}' => mysql_escape_string($e->getMessage())));
            $this->addMessage($message);
            UsniAdaptor::app()->end();
        }
    }

    /**
     * Writes config file.
     * @return void
     */
    public function writeConfigFile()
    {
        $this->addMessage(UsniAdaptor::t('install', 'Start writing configuration file'));
        //Read the config file from framework
        $configFilePath             = UsniAdaptor::getAlias('@usni/library/config');
        $configFile                 = $configFilePath . '/instance.install.php';
        copy($configFile, $this->targetConfigFilePath);
        chmod($this->targetConfigFilePath, 0777);
        $this->replaceConfigVariables($this->targetConfigFilePath);
        //Test file
        $testConfigFile             = $configFilePath . '/instance.install.test.php';
        copy($testConfigFile, $this->targetTestConfigFilePath);
        chmod($this->targetTestConfigFilePath, 0777);
        $this->replaceConfigVariables($this->targetTestConfigFilePath, 'test');
        //$this->setTestConstants($this->targetTestConfigFilePath);
        //Instance config file
        $instanceConfigFile         = $configFilePath . '/instance.config.php';
        copy($instanceConfigFile, $this->targetInstanceConfigFilePath);
        chmod($this->targetInstanceConfigFilePath, 0777);
        $this->addMessage(UsniAdaptor::t('install', 'Configuration files created successfully'));
        $this->addProgressMessage('90');
    }

    /**
     * Replace the config variables.
     * @param string $configFile
     * @param $environment string
     * @return void
     */
    public function replaceConfigVariables($configFile, $environment = null)
    {
        $model      = $this->configModel;
        $content    = file_get_contents($configFile);
        $content    = str_replace('{{Application}}', $model->siteName, $content);
        $content    = str_replace('{{hostName}}', $model->dbHost, $content);
        $content    = str_replace('{{dbPort}}', $model->dbPort, $content);
        $content    = str_replace('{{dbName}}', $model->dbName, $content);
        $content    = str_replace('{{dbUserName}}', $model->dbUsername, $content);
        $content    = str_replace('{{dbPassword}}', $model->dbPassword, $content);
        $content    = str_replace('{{testDbName}}', strtolower($model->dbName). '-test', $content);
        $content    = preg_replace('/\$installed\s*=\s*false;/', '$installed = true;', $content);
        if($environment == null)
        {
            $content    = str_replace('{{environment}}', $model->environment, $content);
        }
        else
        {
            $content    = str_replace('{{environment}}', $environment, $content);
        }
        $content    = str_replace('{{frontTheme}}', $model->frontTheme, $content);
        file_put_contents($configFile, $content);
    }

    /**
     * Replace the config variables.
     * @return void
     */
    /*public function setTestConstants($configFile)
    {
        $content    = file_get_contents($configFile);
        $hostInfo   = RequestUtil::getDefaultHostInfo();
        if(YII_ENV == 'test')
        {
            $url        = $hostInfo . '/' . basename(APPLICATION_PATH);
        }
        else
        {
            $scriptUrl  = RequestUtil::getDefaultScriptUrl(UsniAdaptor::app()->controller->getRoute());
            $url        = $hostInfo . $scriptUrl;
        }
        $content    = preg_replace('/\'TEST_BASE_URL\', \'\'/', '\'TEST_BASE_URL\', \'' . $url . '/index-test.php\'', $content);
        $content    = preg_replace('/\'TEST_SCRIPT_URL\', \'\'/', '\'TEST_SCRIPT_URL\', \'' . $url . '\'', $content);
        file_put_contents($configFile, $content);
    }*/

    /**
     * Save configuration in database.
     * @return void
     */
    public function saveSettingsInDatabase()
    {
        $configModel = $this->configModel;
        foreach ($configModel->getAttributes() as $attribute => $value)
        {
            ConfigurationUtil::insertOrUpdateConfiguration('application', $attribute, $value);
        }
        //Set db schema caching
        ConfigurationUtil::insertOrUpdateConfiguration('application', 'enableSchemaCache', true);
        ConfigurationUtil::insertOrUpdateConfiguration('application', 'schemaCachingDuration', 3600);
        $this->addMessage(UsniAdaptor::t('install', 'Configuration saved successfully'));
        $this->addProgressMessage('55');
    }

    /**
     * Loads data into the database.
     * @return void
     */
    public function loadData()
    {
        $path           = UsniAdaptor::app()->getRuntimePath();
        @unlink($path . '/installdefaultdata.bin');
        @unlink($path . '/installdemodata.bin');
        //Create super user
        UserUtil::createSuperUser($this->configModel->getAttributes());
        $this->addMessage(UsniAdaptor::t('install', 'Super user created successfully'));
        $this->addProgressMessage('60');

        //Load permissions
        //TODO @Mayank Not needed at this point in time as we are handling using groups.
        $this->addMessage(UsniAdaptor::t('install', 'Start loading module permissions'));
        AuthManager::addModulesPermissions();
        $this->addMessage(UsniAdaptor::t('install', 'Module permissions loaded successfully'));
        $this->addProgressMessage('65');
        
        //Load modules data
        $loadDemoData   = (bool)$this->configModel->demoData;
        $modules        = UsniAdaptor::app()->moduleManager->getInstantiatedModules();
        //Insert data from application data manager.
        $appDataManager = get_class(UsniAdaptor::app()->globalDataManager);
        $appDataManager::loadDefaultData();
        $this->installDefaultAndDemoData($modules, $loadDemoData);
        $this->addMessage(UsniAdaptor::t('install', 'Default data loaded successfully'));
        if($loadDemoData === true)
        {
           $this->addMessage(UsniAdaptor::t('install', 'Demo data loaded successfully'));
        }
        $this->loadInstanceData();
        $this->addProgressMessage('80');
    }
    
    /**
     * Loads instance data
     * @return void
     */
    public function loadInstanceData()
    {
        $transaction    = UsniAdaptor::db()->beginTransaction();
        try
        {
            $basePath = APPLICATION_PATH . '/data';
            //Check if instance sql exists or not.
            $databaseInstanceDataFile = $basePath . '/instancedbdata.sql';
            if(file_exists($databaseInstanceDataFile))
            {
                $sql  = file_get_contents($databaseInstanceDataFile);
                if(!empty($sql))
                {
                    UsniAdaptor::db()->createCommand($sql)->execute();
                }
            }
            $transaction->commit();
            UsniAdaptor::db()->getSchema()->refresh();
            $this->addMessage(UsniAdaptor::t('install', 'Adding instance data successfull'));
        }
        catch (\yii\db\Exception $e)
        {
            $template           = UiHtml::script("$('#progress-container').hide();
                                                  $('#install-errors').removeClass('hide').addClass('show');
                                                  $('#error-messages').html('{message}')");
            $this->obStreamer->setTemplate($template);
            $message = UsniAdaptor::t('install', 'Addition of instance data fails ls with error {error}', array('error' =>  mysql_real_escape_string($e->getMessage())));
            $this->addMessage($message);
            $transaction->rollback();
            UsniAdaptor::app()->end();
        }
    }
    
    /**
     * Install default and demo data.
     * @param array $modules
     * @param bool $loadDemoData
     * @param string $parentId Parent module id
     * @return void
     */
    public function installDefaultAndDemoData($modules, $loadDemoData, $parentId = null)
    {
        $languageModule = $modules['language'];
        $this->processDataInstall($languageModule, 'language', $loadDemoData);
        foreach($modules as $key => $module)
        {
            if(is_array($module))
            {
                if($parentId == null)
                {
                    $module = UsniAdaptor::app()->getModule($key);
                }
                else
                {
                    $module = UsniAdaptor::app()->getModule($parentId . '/' . $key);
                }
            }
            
            if($key == 'debug' || $key == 'language')
            {
                continue;
            }
            $this->processDataInstall($module, $key, $loadDemoData);
        }
    }
    
    /**
     * Process data install
     * @param Module $module
     * @param string $key
     * @param bool $loadDemoData
     */
    protected function processDataInstall($module, $key, $loadDemoData)
    {
        if($module->getDataManager() == null)
        {
            $dmPaths          = $module->getDataManagerPath();
            foreach($dmPaths as $dmPath)
            {
                $dmPathAlias      = str_replace('\\', '/', $dmPath);
                $dmPathKey        = UsniAdaptor::getAlias($dmPathAlias);
                $managerClassName = ucfirst($key) . 'DataManager';
                $managerClass     = $dmPath . '\\' . $managerClassName;
                $rawPath          = $dmPathKey . DS . $managerClassName . '.php';
                if(file_exists($rawPath))
                {
                    //$this->addMessage(UsniAdaptor::t('install', 'Start adding default data for module ' . $key));
                    $managerClass::loadDefaultData();
                    $this->addMessage(UsniAdaptor::t('install', 'Default data added for module ' . $key));
                    if($loadDemoData)
                    {
                        if(method_exists($managerClass, 'loadDemoData'))
                        {
                            //$this->addMessage(UsniAdaptor::t('install', 'Start adding demo data for module ' . $key));
                            $managerClass::loadDemoData();
                            $this->addMessage(UsniAdaptor::t('install', 'Demo data added for module ' . $key));
                        }
                    }
                }
            }
        }
        else
        {
            $dataManager  = $module->getDataManager();
            $dmPathAlias  = str_replace('\\', '/', $dataManager);
            $managerClass = FileUtil::normalizePath(UsniAdaptor::getAlias($dmPathAlias) . '.php');
            if(file_exists($managerClass))
            {
                $dataManager::loadDefaultData();
                $this->addMessage(UsniAdaptor::t('install', 'Default data added for module ' . $key));
                if($loadDemoData)
                {
                    if(method_exists($dataManager, 'loadDemoData'))
                    {
                        //$this->addMessage(UsniAdaptor::t('install', 'Start adding demo data for module ' . $key));
                        $dataManager::loadDemoData();
                        $this->addMessage(UsniAdaptor::t('install', 'Demo data added for module ' . $key));
                    }
                }
            }
        }
    }

    /**
     * Get install environments.
     * @return array
     */
    public static function getEnvironments()
    {
        return [
                    'dev'           => UsniAdaptor::t('install', 'Development'),
                    'staging'       => UsniAdaptor::t('install', 'Staging'),
                    'production'    => UsniAdaptor::t('install', 'Production')
               ];
    }

    /**
     * Get debug mode bool value.
     * @param int $value
     * @return boolean
     */
    public static function getDebugMode($value)
    {
        if($value == 1)
        {
            return true;
        }
        return false;
    }

    /**
     * Get available themes.
     * @return array
     */
    public static function getAvailableThemes()
    {
        return UsniAdaptor::app()->themeManager->getThemeNames();
    }

    /**
     * Loads demo data.
     * @return void
     */
    public function backupDatabase()
    {
        $this->addMessage(UsniAdaptor::t('install', 'Back up database starts'));
        $dbHost         = $this->configModel->dbHost;
        $dbUsername     = $this->configModel->dbUsername;
        $dbPassword     = $this->configModel->dbPassword;
        $dbPort         = $this->configModel->dbPort;
        $dbName         = $this->configModel->dbName;
        $basePath       = APPLICATION_PATH . '/data';
        $filePath       = $basePath . '/installdata.sql';
        DatabaseUtil::backupDatabase($dbHost,
                                     $dbUsername,
                                     $dbPassword,
                                     $dbPort,
                                     $dbName, $filePath);
        $this->addMessage(UsniAdaptor::t('install', 'Back up database successfull'));
    }

    /**
     * Reload install data.
     */
    public static function reloadInstallData()
    {
        $dbHost     = ConfigurationUtil::getValue('application', 'dbHost');
        $dbUsername = ConfigurationUtil::getValue('application', 'dbUsername');
        $dbPassword = ConfigurationUtil::getValue('application', 'dbPassword');
        $dbPort     = ConfigurationUtil::getValue('application', 'dbPort');
        $dbName     = ConfigurationUtil::getValue('application', 'dbName');
        $basePath   = APPLICATION_PATH . '/data';
        $filePath   = $basePath . '/installdata.sql';
        DatabaseUtil::restoreDatabase($dbHost,
                                     $dbUsername,
                                     $dbPassword,
                                     $dbPort,
                                     $dbName, $filePath);
    }
    
    /**
     * Build database tables.
     * @return void
     */
    public function buildTables()
    {
        UsniAdaptor::db()->createCommand("SET foreign_key_checks = 0;")->execute();
        UsniAdaptor::db()->createCommand("SET CHARACTER SET utf8;")->execute();
        $modules        = UsniAdaptor::app()->moduleManager->getInstantiatedModules();
        try
        {
            //Build tables from application table manager.
            $appTableManager = new \usni\library\managers\TableManager();
            $appTableManager->buildTables();
            foreach($modules as $key => $module)
            {
                if($key == 'debug')
                {
                    continue;
                }
                $managerClass   = $module->getTableManager();
                $tmPath         = UsniAdaptor::getAlias('@' . str_replace('\\', '/', $managerClass)) . '.php';
                if(file_exists($tmPath))
                {
                    //$this->addMessage(UsniAdaptor::t('install', 'Start creating tables for module ' . $key));
                    $manager = new $managerClass();
                    $manager->buildTables();
                    $this->addMessage(UsniAdaptor::t('install', 'Create tables for module ' . $key . ' is successful'));
                }
            }
        }
        catch(\Exception $e)
        {
            //TODO Not clear as why not getting displayed
            $this->addMessage(UsniAdaptor::t('install', 'Install fails with error ' . $e->getMessage()));
            throw $e;
        }
    }
    
    /**
     * Add message
     * @param string $message
     */
    public function addMessage($message)
    {
        if($this->showBufferMessage)
        {
            $this->obStreamer->add($message);
        }
        else
        {
            echo $message . "\n";
        }
    }
    
    /**
     * Add message
     * @param string $message
     */
    public function addProgressMessage($message)
    {
        if($this->showBufferMessage)
        {
            $this->obStreamer->addProgressMessage($message);
        }
        else
        {
            echo $message . "\n";
        }
    }
}