<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl.html
 */
namespace usni\library\modules\users\managers;

use usni\library\modules\users\models\User;
use usni\library\managers\BaseMenuManager;
use usni\UsniAdaptor;
/**
 * MenuManager class file.
 * @package usni\library\modules\users\managers
 */
class MenuManager extends BaseMenuManager
{
    /**
     * @inheritdoc
     */
    public static function getModelClassName()
    {
        return User::className();
    }
    
    /**
     * @inheritdoc
     */
    public static function getIcon()
    {
        return 'user';
    }
    
    /**
     * @inheritdoc
     */
    public static function getModuleUniqueId()
    {
        return 'users';
    }
    
    /**
     * @inheritdoc
     */
    public static function getSidebarHeader()
    {
        return UsniAdaptor::t('application', 'System');
    }
}