<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\library\utils\CacheUtil;
use usni\UsniAdaptor;
use usni\library\utils\ConfigurationUtil;
use usni\library\utils\ArrayUtil;
use usni\fontawesome\FA;
use usni\library\components\UiHtml;
use usni\library\components\UiWebModule;
/**
 * AdminMenuRenderer class file.
 * 
 * @package usni\library\components
 */
class AdminMenuRenderer extends \yii\base\Component
{
    /**
     * Get sidebar menu header items
     * @return array
     */
    public static function getSidebarMenuHeaderItems()
    {
        $model      = UsniAdaptor::app()->user->getUserModel();
        $menuItems  = CacheUtil::get($model->username . '-allModulesMenuHeaderItems');
        if($menuItems === false)
        {
            \Yii::trace("All menu header items are not in cache");
            $modules    = UsniAdaptor::app()->moduleManager->getInstantiatedModules();
            $menuItems  = array();
            foreach ($modules as $key => $module)
            {
                if(is_subclass_of($module->module, UiWebModule::className()))
                {
                    continue;
                }
                if($key == 'debug')
                {
                    continue;
                }
                if((bool)ConfigurationUtil::isModuleEnabled($key) == false)
                {
                    continue;
                }
                $menuManager    = $module->getMenuManager();
                if(class_exists($menuManager))
                {
                    $menuManagerInstance    = new $menuManager();
                    if(method_exists($menuManagerInstance, 'getSidebarHeader'))
                    {
                        $menuItems[$key] = $menuManagerInstance->getSidebarHeader();
                    }
                }
                else
                {
                    continue;
                }
            }
            CacheUtil::set($model->username . '-allModulesMenuHeaderItems', serialize($menuItems));
        }
        else
        {
            \Yii::trace("All menu header items picked from cache");
            $menuItems = unserialize($menuItems);
        }
        return $menuItems;
    }
    
    /**
     * Get all menu items for the modules
     * @return array
     */
    public static function getItems()
    {
        $model      = UsniAdaptor::app()->user->getUserModel();
        $menuItems  = CacheUtil::get($model->username . '-allModulesMenuItems');
        if($menuItems === false)
        {
            \Yii::trace("All menu items are not in cache");
            $modules    = UsniAdaptor::app()->moduleManager->getInstantiatedModules();
            $menuItems  = array();
            foreach ($modules as $key => $module)
            {
                if(is_subclass_of($module->module, UiWebModule::className()))
                {
                    continue;
                }
                if($key == 'debug')
                {
                    continue;
                }
                if((bool)ConfigurationUtil::isModuleEnabled($key) == false)
                {
                    continue;
                }
                $menuManager    = $module->getMenuManager();
                if(class_exists($menuManager))
                {
                    $menuManagerInstance    = new $menuManager();
                    $menuItems[$key]        = $menuManagerInstance->getItems();
                }
                else
                {
                    continue;
                }
            }
            CacheUtil::set($model->username . '-allModulesMenuItems', serialize($menuItems));
        }
        else
        {
            \Yii::trace("All menu items picked from cache");
            $menuItems = unserialize($menuItems);
        }
        return $menuItems;
    }
    
    /**
     * Get sidebar items.
     * @return array
     */
    public static function getSidebarItems()
    {
        $sidebarItems        = [];
        $menuItemsCollection = static::getItems();
        foreach($menuItemsCollection as $key => $collection)
        {
            if(($items = ArrayUtil::getValue($collection, 'sidebar')) != null)
            {
                $sidebarItems[$key] = $items;
            }
        }
        return $sidebarItems;
    }
    
    /**
     * Get create items.
     * @return array
     */
    public static function getCreateItems()
    {
        $createItems        = [];
        $menuItemsCollection = static::getItems();
        foreach($menuItemsCollection as $key => $collection)
        {
            if(($items = ArrayUtil::getValue($collection, 'create')) != null)
            {
                if(!empty($items))
                {
                    $createItems[$key] = $items;
                }
            }
        }
        return $createItems;
    }
    
    /**
     * Get manage items.
     * @return array
     */
    public static function getManageItems()
    {
        $manageItems        = [];
        $menuItemsCollection = static::getItems();
        foreach($menuItemsCollection as $key => $collection)
        {
            if(($items = ArrayUtil::getValue($collection, 'manage')) != null)
            {
                if(!empty($items))
                {
                    $manageItems[$key] = $items;
                }
            }
        }
        return $manageItems;
    }
    
    /**
     * Get sidebar menu items
     * @return string
     */
    public static function getSideBarMenuItems()
    {
        $menuItems  = static::getSidebarItems();
        return ArrayUtil::merge(self::getApplicationMenuItems(), $menuItems);
    }
    
    /**
     * Gets menu items that are defined at the application level and not specific to any module.
     * @return array
     */
    public static function getApplicationMenuItems()
    {
        $label = UsniAdaptor::t('application', 'Dashboard');
        $labelWithTag = UiHtml::tag('span', $label);
        return array(
                        'dashboard' => [
                            [
                                'label'         => self::getSidebarMenuIcon('dashboard') .
                                                   $labelWithTag,
                                'itemOptions'   => array('class' => 'navblock-header'),
                                'url'           => array('/home/default/dashboard'),
                            ]
                        ]
                    );
    }
    
    /**
     * Gets admin sidebar icon.
     * @param string $icon
     * @return string
     */
    public static function getSidebarMenuIcon($icon)
    {
        return FA::icon($icon)->size(FA::SIZE_LARGE);
    }

    /**
     * Render create menu.
     * @param User $user
     * @return array
     */
    public static function renderCreateMenu($user)
    {
        return self::renderTopNavMenu($user, 'topnavCreateMenu', UsniAdaptor::t('application', 'Create'), 'create');
    }
    
    /**
     * Render manage menu.
     * @param User $user
     * @return array
     */
    public static function renderManageMenu($user)
    {
        return self::renderTopNavMenu($user, 'topnavManageMenu', UsniAdaptor::t('application', 'Manage'), 'manage');
    }
    
    /**
     * Render topnav menu.
     * @param User $user
     * @param string $cacheKey
     * @param string $menuLabel
     * @param string $type
     * @return array
     */
    public static function renderTopNavMenu($user, $cacheKey, $menuLabel, $type)
    {
        $content = CacheUtil::get($cacheKey);
        if($content === false)
        {
            if($type == 'manage')
            {
                $menuItems = self::getManageItems();
            }
            else
            {
                $menuItems = self::getCreateItems();
            }
            if(count($menuItems) > 0 )
            {
                $headerLink    = FA::icon('plus') . "\n" .
                                 UiHtml::tag('span', $menuLabel, ['class' => 'topnav-create']) . "\n" .
                                 FA::icon('caret-down');
                $headerLink    = UiHtml::a($headerLink, '#', ['data-toggle' => 'dropdown', 'class' => 'dropdown-toggle']);
                $listItems     = null;
                foreach($menuItems as $moduleKey => $items)
                {
                    foreach($items as $item)
                    {
                        $itemLink   = UiHtml::a($item['label'], UsniAdaptor::createUrl($item['url']));
                        $listItems .= UiHtml::tag('li', $itemLink);
                    }
                }
                $listItems     = UiHtml::tag('ul', $listItems, ['class' => 'pull-right dropdown-menu']);
                $content    = $headerLink . $listItems;
            }
            else
            {
                $content = null;
            }
            CacheUtil::set($cacheKey, $content);
        }
        return $content;
    }
}