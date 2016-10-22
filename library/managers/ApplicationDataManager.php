<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\managers;

use usni\library\components\UiDataManager;
use usni\library\utils\ConfigurationUtil;
/**
 * ApplicationDataManager class file.
 *
 * @package usni\library\managers
 */
class ApplicationDataManager extends UiDataManager
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
        $sortOrder = serialize(array('auth', 'users', 'notification', 'service'));
        ConfigurationUtil::insertOrUpdateConfiguration('application', 'sortOrder', $sortOrder);
        ConfigurationUtil::insertOrUpdateConfiguration('application', 'appRebuild', false);
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
