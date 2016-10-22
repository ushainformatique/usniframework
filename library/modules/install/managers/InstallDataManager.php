<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\install\managers;

use usni\library\components\UiDataManager;
use usni\library\utils\ConfigurationUtil;
use usni\UsniAdaptor;
/**
 * Loads data related to install module.
 * 
 * @package usni\library\modules\install\managers
 */
class InstallDataManager extends UiDataManager
{
    /**
     * @inheritdoc
     */
    public static function loadDefaultData()
    {
        $installedData  = static::getUnserializedData('installdefaultdata.bin');
        $isDataLoaded   = static::checkIfClassDataLoaded($installedData);
        if($isDataLoaded)
        {
            return false;
        }
        $siteName = ConfigurationUtil::getValue('application', 'siteName');
        if($siteName == null)
        {
            $metaKeywords       = UsniAdaptor::t('install', 'My Demo Site Keywords');
            $metaDescription    = UsniAdaptor::t('install', 'My Demo Site Description');
        }
        else
        {
            $metaKeywords       = $siteName . ' ' . UsniAdaptor::t('install', 'Keywords');
            $metaDescription    = $siteName . ' ' . UsniAdaptor::t('application', 'Description');
        }
        ConfigurationUtil::insertOrUpdateConfiguration('application', 'metaKeywords', $metaKeywords);
        ConfigurationUtil::insertOrUpdateConfiguration('application', 'metaDescription', $metaDescription);
        ConfigurationUtil::insertOrUpdateConfiguration('application', 'isRegistrationAllowed', 1);
        static::writeFileInCaseOfOverRiddenMethod('installdefaultdata.bin');
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public static function loadDemoData()
    {
        return;
    }
    
    /**
     * @inheritdoc
     */
    public static function getModelClassName()
    {
        return null;
    }
}