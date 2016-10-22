<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;
use usni\UsniAdaptor;
use usni\library\utils\FileUtil;
use usni\library\utils\ConfigurationUtil;
use usni\library\utils\ApplicationUtil;
/**
 * UiConsoleApplication extends Application by providing functions specific to the console application.
 * @package usni\library\components
 */
class UiConsoleApplication extends \yii\console\Application
{
    /**
     * Check if application is installed.
     * @var boolean
     */
    public $installed;

    /**
     * Contains modules that are instantiated.
     * @var array
     */
    private $_instantiatedModules;

    /**
     * Modules excluded from autoload.
     * @var array
     */
    private static $_excludedModulesFromAutoload = array('gii', 'users');

    /**
     * Environment in which application is running dev/test/prod
     * @var string
     */
    public $environment;
    
    /**
     * Display name for the application 
     * @var string 
     */
    public $displayName;
    
    /**
     * Selected theme for the front end.
     * @var string
     */
    public $frontTheme;
    
    /**
     * Powered by url for the application 
     * @var string 
     */
    public $poweredByUrl;
    
    /**
     * Extended models configuration. This consist of rules, labels, hints etc.
     * @var array 
     */
    public $extendedModelsConfig;
    
    /**
     * Initialises the web application.
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->setCacheComponent();
        $this->processModules();
        $this->setDatabaseConfig();
        ApplicationUtil::loadAdditionalModuleConfig('@console/config/moduleconfig.php');
    }

    /**
     * @inheritdoc
     */
    public function setRuntimePath($path)
    {
        $runtimePath = realpath($path);
        if ($runtimePath === false)
        {
            FileUtil::createDirectory($path);
        }
        parent::setRuntimePath($path);
    }

    /**
     * Checks if application is installed or not.
     * @return boolean
     */
    public function isInstalled()
    {
        return $this->installed;
    }

    /**
     * Checks if the application is in debug mode.
     * @return boolean
     */
    public function isDebugMode()
    {
        return $this->debugMode;
    }

    /**
     * Sets the cache component for the application.
     * @return void
     */
    public function setCacheComponent()
    {
        if(!is_object(UsniAdaptor::app()->cache))
        {
            UsniAdaptor::app()->set('cache', new \yii\caching\DummyCache());
        }
    }

    /**
     * Checks if site is in maintenance mode.
     * @return boolean
     */
    public function isMaintenanceMode()
    {
        $siteMaintenance = ConfigurationUtil::getValue('application', 'siteMaintenance');
        if ($siteMaintenance == null)
        {
            return false;
        }
        else
        {
            if (intval($siteMaintenance) == 0)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }

    /**
     * @return UiMaintenanceManager the maintenance manager component
     */
    public function getMaintenanceManager()
    {
        return $this->get('maintenanceManager');
    }

    /**
     * Returns a string that can be displayed on your Web page showing Powered-by-usni information
     * @return string a string that can be displayed on your Web page showing Powered-by-usni information
     */
    public function powered()
    {
		return UsniAdaptor::t('application','Powered by {application}.', array('application'=>'<a href="' . UsniAdaptor::app()->poweredByUrl . '" rel="external">' . UsniAdaptor::app()->displayName . '</a>'));
    }

    /**
     * Checks if rebuild is in progress.
     * @return bool
     */
    public function isRebuildInProgress()
    {
        $isRebuild = file_exists(UsniAdaptor::app()->getRuntimePath() . '/rebuildstate.bin');
        return (bool)$isRebuild;
    }

    /**
     * Is demo application
     * @return boolean
     */
    public function isDemoApplication()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getModule($id, $load = true)
    {
        $instantiatedModules = $this->_instantiatedModules;
        if(isset($instantiatedModules[$id]))
        {
            return $instantiatedModules[$id];
        }
        else
        {
            return parent::getModule($id, $load);
        }
    }
    
    /**
     * Process Modules
     * 
     * @return void
     */
    protected function processModules()
    {
        UsniAdaptor::app()->moduleManager->bootstrap();
    }
    
    /**
     * Sets database config.
     * 
     * @return void
     */
    protected function setDatabaseConfig()
    {
        if($this->installed)
        {
            UsniAdaptor::db()->enableSchemaCache = ConfigurationUtil::getValue('application', 'enableSchemaCache');
            UsniAdaptor::db()->schemaCacheDuration = ConfigurationUtil::getValue('application', 'schemaCachingDuration');
        }
    }
}
?>