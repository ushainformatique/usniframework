<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl.html
 */
namespace usni\library\managers;

use usni\UsniAdaptor;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\components\AdminMenuRenderer;
use usni\library\exceptions\MethodNotImplementedException;
use usni\library\utils\AdminUtil;
/**
 * BaseMenuManager class file.
 * 
 * @package usni\library\managers
 */
class BaseMenuManager
{
    /**
     * Get items.
     * @return array
     */
    public static function getItems()
    {
        $items = [];
        static::getSidebarItems($items);
        static::getCreateItems($items);
        static::getManageItems($items);
        return $items;
    }
    
    /**
     * Get sidebar items.
     * @param array $items
     */
    public static function getSidebarItems(& $items)
    {
        $items ['sidebar']   = [];
        $modelClassName      = static::getModelClassName();
        $user                = UsniAdaptor::app()->user->getUserModel();
        $moduleUniqueId      = static::getModuleUniqueId();
        if(strpos($moduleUniqueId, '/') > 0)
        {
            $data       = explode('/', $moduleUniqueId);
            $moduleId   = $data[0];
        }
        else
        {
            $moduleId = $moduleUniqueId;
        }
        if(AuthManager::checkAccess($user, 'access.' . $moduleId))
        {
            $label             = AdminUtil::wrapSidebarMenuLabel(static::getLabel($modelClassName));
            $items ['sidebar'] =    [
                                        [
                                        'label'       => static::renderIcon() . 
                                                         $label,
                                        'url'         => [static::getManageUrl()],
                                        'itemOptions' => ['class' => 'navblock-header']
                                    ]
                            ];
        }
    }
    
    /**
     * Get label for the menu item
     * @param string $modelClassName
     * @return string
     */
    public static function getLabel($modelClassName)
    {
        return $modelClassName::getLabel(2);
    }

    /**
     * Get create items.
     * @param array $items
     * @return void
     */
    public static function getCreateItems(& $items)
    {
        $items ['create']    = [];
        $modelClassName      = static::getModelClassName();
        $shortModelClassName = strtolower(UsniAdaptor::getObjectClassName($modelClassName));
        $user  = UsniAdaptor::app()->user->getUserModel();
        if(AuthManager::checkAccess($user, $shortModelClassName . '.create'))
        {
            $items['create'] = [
                                [
                                    'label' => $modelClassName::getLabel(1),
                                    'url'   => static::getCreateUrl()
                                ]
                            ];
        }
    }
    
    /**
     * Get manage items.
     * @param array $items
     * @return void
     */
    public static function getManageItems(& $items)
    {
        $items ['manage']    = [];
        $modelClassName      = static::getModelClassName();
        $shortModelClassName = strtolower(UsniAdaptor::getObjectClassName($modelClassName));
        $user  = UsniAdaptor::app()->user->getUserModel();
        if(AuthManager::checkAccess($user, $shortModelClassName . '.manage'))
        {
            $items['manage'] = [
                                [
                                    'label' => $modelClassName::getLabel(2),
                                    'url'   => static::getManageUrl()
                                ]
                            ];
        }
    }
    
    /**
     * Get model class name.
     * @return string
     * @throws MethodNotImplementedException
     */
    public static function getModelClassName()
    {
        throw new MethodNotImplementedException();
    }
    
    /**
     * Get icon string
     * @return string
     */
    public static function renderIcon()
    {
        $icon = static::getIcon();
        $sidebarHeader = static::getSidebarHeader();
        if($icon != null && $sidebarHeader == null)
        {
            return AdminMenuRenderer::getSidebarMenuIcon($icon);
        }
        return null;
    }
    
    /**
     * Get icon.
     * @return string
     */
    public static function getIcon()
    {
        return null;
    }
    
    /**
     * Get manage url
     * @return string
     */
    public static function getManageUrl()
    {
        $uniqueId = static::getModuleUniqueId();
        return '/' . $uniqueId . '/default/manage';
    }
    
    /**
     * Get create url
     * @return string
     */
    public static function getCreateUrl()
    {
        $uniqueId = static::getModuleUniqueId();
        return '/' . $uniqueId . '/default/create';
    }
    
    /**
     * Get module id.
     * @return string
     * @throws MethodNotImplementedException
     */
    public static function getModuleUniqueId()
    {
        throw new MethodNotImplementedException();
    }
    
    /**
     * Get sidebar header under which module would fall
     * @return string
     */
    public static function getSidebarHeader()
    {
        return null;
    }
}