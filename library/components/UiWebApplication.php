<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\UsniAdaptor;
use usni\library\utils\ConfigurationUtil;
use usni\library\utils\FileUtil;
use usni\library\utils\RequestUtil;
use usni\library\utils\CacheUtil;
use usni\library\utils\ArrayUtil;
use usni\library\utils\ApplicationUtil;

/**
 * UiWebApplication extends WebApplication by providing functions specific to the application.
 * 
 * @package usni\library\components
 */
class UiWebApplication extends \yii\web\Application
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
    private static $_excludedModulesFromAutoload = array('gii');

    /**
     * Environment in which application is running dev/test/prod
     * @var string
     */
    public $environment;

    /**
     * Checks if the application is in debug mode.
     * @var boolean
     */
    public $debugMode;

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
     * Display name for the application 
     * @var string 
     */
    public $displayName;
    
    /**
     * Powered by for the application 
     * @var string 
     */
    public $poweredByName;
    
    /**
     * Extended models configuration. This consist of rules, labels, hints etc.
     * @var array 
     */
    public $extendedModelsConfig;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setCacheComponent();
        $this->processModules();
        //Set alias for application url independent of backend or frontend
        UsniAdaptor::setAlias('appurl', RequestUtil::getDomainUrl());
        $this->setDatabaseConfig();
        $this->setDateTimeConfig();
    }
    
    /**
     * @inheritdoc
     */
    public function setRuntimePath($path)
    {
        $runtimePath = realpath($path);
        if ($runtimePath === false)
        {
            FileUtil::createDirectory($path, 0777);
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
		return UsniAdaptor::t('application','Powered by {application}.', array('application'=>'<a href="' . UsniAdaptor::app()->poweredByUrl . '" rel="external">' . UsniAdaptor::app()->poweredByName . '</a>'));
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
        $instantiatedModules = UsniAdaptor::app()->moduleManager->getInstantiatedModules();
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
    public function processModules()
    {
        $rebuildData = UsniAdaptor::getRequestParam('rebuildModuleMetadata', 'false');
        if($rebuildData == 'true')
        {
            ApplicationUtil::rebuildModuleMetadata();
            CacheUtil::loadConfiguration();
        }
        $clearCache = UsniAdaptor::getRequestParam('clearCache', 'false');
        if($clearCache === 'true' && $this->isInstalled())
        {
            //Clear the configuration so that fresh values are loaded into cache
            CacheUtil::delete('appconfig');
            CacheUtil::loadConfiguration();
        }
        UsniAdaptor::app()->moduleManager->bootstrap();
    }
    
    /**
     * Sets database config.
     * 
     * @return void
     */
    public function setDatabaseConfig()
    {
        if($this->installed)
        {
            UsniAdaptor::db()->enableSchemaCache    = ConfigurationUtil::getValue('application', 'enableSchemaCache');
            UsniAdaptor::db()->schemaCacheDuration  = ConfigurationUtil::getValue('application', 'schemaCachingDuration');
        }
    }
    
    /**
     * Sets date time config.
     * @return void
     */
    public function setDateTimeConfig()
    {
        if($this->installed)
        {
            $appTimezone = ConfigurationUtil::getValue('application', 'timezone');
            if($appTimezone == null)
            {
                date_default_timezone_set('Asia/Kolkata');
            }
            else
            {
                date_default_timezone_set($appTimezone);
            }
        }
    }
    
    /**
     * Sets component in session.
     * 
     * @param $key Session key
     * @param $component Name of the model
     * @param $name Name of the component in session.
     * @return void
     */
    public function setComponentInSession($key, $component, $name)
    {
        $sessionData = $this->getSession()->get($key);
        if($sessionData == null)
        {
            $sessionData = new $component();
            $this->getSession()->set($key, serialize($sessionData));
        }
        else
        {
            $sessionData = unserialize($sessionData);
        }
        $this->set($name, $sessionData);
    }
    
    /**
     * @inheritdoc
     */
    protected function bootstrap()
    {
        if ($this->extensions === null) 
        {
            $file = UsniAdaptor::getAlias('@vendor/yiisoft/extensions.php');
            $this->extensions = is_file($file) ? include($file) : [];
            $file = UsniAdaptor::getAlias('@approot/extensions.php');
            if(file_exists($file))
            {
                $extensions = include($file);
                $this->extensions = ArrayUtil::merge($this->extensions, $extensions);
            }
        }
        parent::bootstrap();
    }
    
    /**
     * Get front url. This would be required where in back end we need front url
     * @return string
     */
    public function getFrontUrl()
    {
        $url = \yii\helpers\Url::base(true);
        if(strpos($url, 'backend') !== false)
        {
            return str_replace('/backend', '', $url);
        }
        return $url;
    }
}