<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\UsniAdaptor;
use usni\library\utils\FileUtil;
use yii\helpers\Json;
use usni\library\utils\ArrayUtil;
/**
 * Class consisting of utility functions related to application.
 * 
 * @package usni\library\utils
 */
class ApplicationUtil
{
    /**
     * Load additional module config.
     * @param string $aliasedPath
     */
    public static function loadAdditionalModuleConfig($aliasedPath)
    {
        $moduleConfigFile = UsniAdaptor::getAlias($aliasedPath);
        if(file_exists($moduleConfigFile))
        {
            $moduleConfig = require(FileUtil::normalizePath($moduleConfigFile));
            foreach($moduleConfig as $moduleKey => $value)
            {
                $module = UsniAdaptor::app()->getModule($moduleKey);
                \Yii::configure($module, $value);
            }
        }
    }
    
    /**
     * Gets multi language dropdown
     * @param string $viewClassName
     * @return string
     */
    public static function getMultilanguageDropDown($viewClassName)
    {
        $view = new $viewClassName(['selectedLanguage' => UsniAdaptor::app()->languageManager->getContentLanguage()]);
        return $view->render();
    }
    
    /**
     * Rebuild module metadata
     */
    public static function rebuildModuleMetadata()
    {
        $dbStoredMetadata   = Json::decode(ConfigurationUtil::getValue('application', 'moduleMetadata'));
        $configMetadata     = Json::decode(UsniAdaptor::app()->moduleManager->buildModuleConfig());
        //Sync module metadata using config with database
        foreach($configMetadata as $key => $metadata)
        {
            $dbMetadata = ArrayUtil::getValue($dbStoredMetadata, $key);
            if(!empty($dbMetadata))
            {
                $configMetadata[$key] = $dbMetadata;
            }
        }
        ConfigurationUtil::insertOrUpdateConfiguration('application', 'moduleMetadata', Json::encode($configMetadata));
        CacheUtil::delete('appconfig');
    }
}