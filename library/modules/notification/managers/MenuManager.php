<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\managers;

use usni\library\modules\notification\models\Notification;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\modules\notification\models\NotificationTemplate;
use usni\library\modules\notification\models\NotificationLayout;
use usni\UsniAdaptor;
/**
 * MenuManager class file.
 * 
 * @package usni\library\modules\notification\managers
 */
class MenuManager extends \usni\library\managers\BaseMenuManager
{
    /**
     * Get sidebar items.
     * @param array $items
     */
    public static function getSidebarItems(& $items)
    {
        $items['sidebar'] = [];
        $user  = UsniAdaptor::app()->user->getUserModel();
        if(AuthManager::checkAccess($user, 'access.notification'))
        {
            $items['sidebar'] =    [
                                        [
                                        'label'       => Notification::getLabel(2),
                                        'itemOptions' => array('class' => 'navblock-header'),
                                        'url'         => '#',
                                        'items'       => array(
                                                                array(
                                                                        'label' => NotificationTemplate::getLabel(2),
                                                                        'url'   => array('/notification/template/manage'),
                                                                        'visible'=> AuthManager::checkAccess($user, 'notificationtemplate.manage'),
                                                                     ),
                                                                array(
                                                                        'label' => NotificationLayout::getLabel(2),
                                                                        'url'   => array('/notification/layout/manage'),
                                                                        'visible'=> AuthManager::checkAccess($user, 'notificationlayout.manage'),
                                                                     ),
                                                                array(
                                                                        'label' => UsniAdaptor::t('notification', 'List All'),
                                                                        'url'   => array('/notification/default/manage'),
                                                                        'visible'=> AuthManager::checkAccess($user, 'notification.manage'),
                                                                     )
                                                                )
                                    ]
                            ];
        }
    }
    
    /**
     * Get create items.
     * @param array $items
     * @return void
     */
    public static function getCreateItems(& $items)
    {
        $user  = UsniAdaptor::app()->user->getUserModel();
        $permissionModels = ['template', 'layout'];
        $createItems = [];
        foreach($permissionModels as $permissionModel)
        {
            $permissionString = 'notification' . $permissionModel;
            if(AuthManager::checkAccess($user, $permissionString . '.create'))
            {
                $createItems[] = [
                                    'label'       => self::getLabel($permissionString),
                                    'url'         => "/notification/$permissionModel/create"
                                 ];
            }
        }
        $items['create'] = $createItems;
    }
    
    /**
     * Get manage items.
     * @param array $items
     * @return void
     */
    public static function getManageItems(& $items)
    {
        $user  = UsniAdaptor::app()->user->getUserModel();
        $permissionModels = ['template', 'layout'];
        $manageItems = [];
        foreach($permissionModels as $permissionModel)
        {
            $permissionString = 'notification' . $permissionModel;
            if(AuthManager::checkAccess($user, $permissionString . '.manage'))
            {
                $manageItems[] = [
                                    'label'       => self::getLabel($permissionString),
                                    'url'         => "/notification/$permissionModel/manage"
                                 ];
            }
        }
        $items['manage'] = $manageItems;
    }
    
    /**
     * Get label based on permission.
     * @param string $permissionString
     * @return string
     */
    public static function getLabel($permissionString)
    {
        if($permissionString == 'notificationtemplate')
        {
            return UsniAdaptor::t('notification', 'Notification Template');
        }
        elseif($permissionString == 'notificationlayout')
        {
            return UsniAdaptor::t('notification', 'Notification Layout');
        }
    }
    
    /**
     * @inheritdoc
     */
    public static function getSidebarHeader()
    {
        return UsniAdaptor::t('application', 'System');
    }
}