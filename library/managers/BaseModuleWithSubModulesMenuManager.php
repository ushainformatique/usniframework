<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl.html
 */
namespace usni\library\managers;

use usni\UsniAdaptor;
use usni\library\exceptions\MethodNotImplementedException;
use usni\library\utils\ArrayUtil;
/**
 * BaseModuleWithSubModulesMenuManager class file.
 * 
 * @package usni\library\managers
 */
class BaseModuleWithSubModulesMenuManager
{
    /**
     * Class constructor.
     * @return void
     */
    public function __construct()
    {
        
    }
    
    /**
     * Get module id.
     * @return string
     * @throws MethodNotImplementedException
     */
    public static function getModuleId()
    {
        throw new MethodNotImplementedException();
    }

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
        return [];
    }
    
    /**
     * Get create items.
     * @param array $items
     * @return void
     */
    public static function getCreateItems(& $items)
    {
        $createItems    = [];
        $subModuleItems = static::getSubModuleItems();
        foreach($subModuleItems as $subModuleId => $menuItems)
        {
            if(ArrayUtil::getValue($menuItems, 'create', false) !== false)
            {
                $createItems   = ArrayUtil::merge($createItems, $menuItems['create']);
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
        $manageItems    = [];
        $subModuleItems = static::getSubModuleItems();
        foreach($subModuleItems as $subModuleId => $menuItems)
        {
            if(ArrayUtil::getValue($menuItems, 'manage', false) !== false)
            {
                $manageItems   = ArrayUtil::merge($manageItems, $menuItems['manage']);
            }
        }
        $items['manage'] = $manageItems;
    }
    
    /**
     * Get sub module items.
     * @return array
     */
    protected static function getSubModuleItems()
    {
        $subModuleItems    = [];
        $parentModule      = UsniAdaptor::app()->getModule(static::getModuleId());
        foreach($parentModule->modules as $subModule)
        {
            $namespace      = $subModule->getNameSpace();
            $managerClass   = $namespace . '\managers\MenuManager'; 
            $instance       = new $managerClass();
            $subModuleItems[$subModule->id]  = $instance->getItems();
        }
        return $subModuleItems;
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