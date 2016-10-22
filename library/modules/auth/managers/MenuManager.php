<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl.html
 */
namespace usni\library\modules\auth\managers;

use usni\library\modules\auth\models\Group;
use usni\library\managers\BaseMenuManager;
use usni\UsniAdaptor;
/**
 * MenuManager class file.
 * @package usni\library\modules\auth\managers
 */
class MenuManager extends BaseMenuManager
{
    /**
     * @inheritdoc
     */
    public static function getModelClassName()
    {
        return Group::className();
    }
    
    /**
     * @inheritdoc
     */
    public static function getIcon()
    {
        return 'group';
    }
    
    /**
     * @inheritdoc
     */
    public static function getModuleUniqueId()
    {
        return 'auth';
    }
    
    /**
     * Get manage url
     * @return string
     */
    public static function getManageUrl()
    {
        $uniqueId = static::getModuleUniqueId();
        return '/' . $uniqueId . '/group/manage';
    }
    
    /**
     * Get create url
     * @return string
     */
    public static function getCreateUrl()
    {
        $uniqueId = static::getModuleUniqueId();
        return '/' . $uniqueId . '/group/create';
    }
    
    /**
     * @inheritdoc
     */
    public static function getSidebarHeader()
    {
        return UsniAdaptor::t('application', 'System');
    }
}