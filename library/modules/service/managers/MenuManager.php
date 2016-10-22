<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl.html
 */
namespace usni\library\modules\service\managers;

use usni\library\managers\BaseMenuManager;
use usni\library\modules\auth\managers\AuthManager;
use usni\UsniAdaptor;
/**
 * MenuManager class file.
 * @package usni\library\modules\service\managers
 */
class MenuManager extends BaseMenuManager
{
    /**
     * @inheritdoc
     */
    public static function getModelClassName()
    {
        return null;
    }
    
    /**
     * @inheritdoc
     */
    public static function getIcon()
    {
        return 'wrench';
    }
    
    /**
     * @inheritdoc
     */
    public static function getModuleUniqueId()
    {
        return 'service';
    }
    
    /**
     * @inheritdoc
     */
    public static function getSidebarItems(& $items)
    {
        $user = UsniAdaptor::app()->user->getUserModel();
        if(AuthManager::checkAccess($user, 'access.service'))
        {
            $items ['sidebar'] =    [
                                        [
                                        'label'       => static::renderIcon() . 
                                                         UsniAdaptor::t('service', 'Services'),
                                        'url'         => ['/service/default/index'],
                                        'itemOptions' => ['class' => 'navblock-header']
                                    ]
                            ];
        }
    }
    
    /**
     * @inheritdoc
     */
    public static function getCreateItems(&$items)
    {
        $items['create'] = [];
    }
    
    /**
     * @inheritdoc
     */
    public static function getManageItems(&$items)
    {
        $items['manage'] = [];
    }
    
    /**
     * @inheritdoc
     */
    public static function getSidebarHeader()
    {
        return UsniAdaptor::t('application', 'System');
    }
}