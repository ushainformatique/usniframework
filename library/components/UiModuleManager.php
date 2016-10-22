<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\UsniAdaptor;
use usni\library\utils\FileUtil;
use yii\helpers\Json;
use usni\library\utils\ConfigurationUtil;
use usni\library\utils\ArrayUtil;
/**
 * UiModuleManager class file.
 * 
 * @package usni\library\components
 */
class UiModuleManager extends \yii\base\Component
{
    /**
     * Contains modules that are instantiated.
     * @var array
     */
    protected $_instantiatedModules;
    
    /**
     * Contains modules that are enabled.
     * @var array
     */
    protected $_enabledModules;

    /**
     * Modules excluded from autoload.
     * @var array
     */
    protected static $_excludedModulesFromAutoload = ['gii'];
    
    /**
     * Initialize modules
     * @return void
     */
    public function bootstrap()
    {
        if (UsniAdaptor::app()->isInstalled()) 
        {
            $moduleMetadata = ConfigurationUtil::getValue('application', 'moduleMetadata');
            if(empty($moduleMetadata))
            {
                $moduleMetadata = $this->buildModuleConfig();
                ConfigurationUtil::insertOrUpdateConfiguration('application', 'moduleMetadata', $moduleMetadata);
            }
        }
        else
        {
            $moduleMetadata = $this->buildModuleConfig();
            //The configuration is not here becuase db is still not created.
        }
        $data = Json::decode($moduleMetadata);
        UsniAdaptor::app()->setModules($data);
        $this->_instantiatedModules = $this->loadInstalledModules(UsniAdaptor::app()->getModules());
        return;
    }
    
    /**
     * Build module configuration.
     * @return void
     */
    public function buildModuleConfig()
    {
        $modulePaths         = static::getModulePaths();
        $moduleConfig        = [];
        foreach($modulePaths as $modulePath)
        {
            $path       = FileUtil::normalizePath($modulePath);
            if(is_dir($path))
            {
                $modules    = scandir($path);
                foreach ($modules as $moduleId) 
                {
                    if($moduleId == '.' || $moduleId == '..')
                    {
                        continue;
                    }
                    if (is_dir($path . DS . $moduleId)) 
                    {
                        $initFile    = $path . DS . $moduleId . DS . 'config' . DS . 'init.php';
                        if(file_exists($initFile))
                        {
                            $data = require($initFile);
                            foreach($data as $key => $config)
                            {
                                $moduleConfig[$key] = $config;
                            }
                        }
                    }
                }
            }
        }
        $moduleMetadata = Json::encode($moduleConfig);
        return $moduleMetadata;
    }

    /**
     * Loads the installed modules.
     * @return void
     */
    public function loadInstalledModules($modules, $parent = null)
    {
        $instantiatedModules = [];
        foreach ($modules as $key => $module)
        {
            if(is_array($module))
            {
                if (!in_array($key, self::$_excludedModulesFromAutoload))
                {
                    $modifiedKey = $key;
                    if($parent != null)
                    {
                        $modifiedKey = $parent . '/' . $key;
                    }
                    $insModule = UsniAdaptor::app()->getModule($modifiedKey, true);
                    $instantiatedModules[$key] = $insModule;
                    $instantiatedModules = ArrayUtil::merge($instantiatedModules, $this->loadInstalledModules($insModule->getModules(), $insModule->getUniqueId()));
                }
            }
            elseif(!in_array($module->id, self::$_excludedModulesFromAutoload))
            {
                $insModule = $module;
                $instantiatedModules[$module->id] = $module;
            }
        }
        return $instantiatedModules;
    }

    /**
     * Returns a value indicating whether the specified module is instantiated.
     * @param string $id The module ID.
     * @return boolean whether the specified module is installed.
     */
    public function hasModuleInstantiated($id)
    {
        return isset($this->_instantiatedModules[$id]);
    }

    /**
     * Returns the configuration of the currently instantiated modules.
     * @return array the configuration of the currently instantiated modules (module ID => configuration)
     */
    public function getInstantiatedModules()
    {
        return $this->_instantiatedModules;
    }
    
    /**
     * Gets instantiated module keys.
     * @return array
     */
    public function getInstantiatedModulesKeys()
    {
        return array_keys($this->_instantiatedModules);
    }
    
    /**
     * Get module paths.
     * @return array
     */
    protected static function getModulePaths()
    {
        $frameworkModulePath = UsniAdaptor::getAlias('@usni/library/modules');
        $commonModulePath    = UsniAdaptor::getAlias('@common/modules');
        $backendModulePath   = UsniAdaptor::getAlias('@backend/modules');
        $frontendModulePath  = UsniAdaptor::getAlias('@frontend/modules');
        return [$frameworkModulePath, $commonModulePath, $backendModulePath, $frontendModulePath];
    }
    
    /**
     * Get enabled module
     * @return array
     */
    public function getEnabledModules()
    {
        return $this->_enabledModules;
    }
}