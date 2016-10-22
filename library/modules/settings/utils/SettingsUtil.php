<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\utils;

use usni\UsniAdaptor;
use usni\library\components\UiHtml;
use usni\library\modules\auth\managers\AuthManager;
use usni\fontawesome\FA;
use yii\bootstrap\Dropdown;
use usni\library\utils\CacheUtil;
use usni\library\modules\users\utils\UserUtil;
/**
 * Contains utility functions related to settings.
 * @package usni\library\modules\settings\utils
 */
class SettingsUtil
{
    /**
     * Get top navigation items.
     * @return array
     */
    public static function getTopNavItems()
    {
        $items = array();
        $user  = UsniAdaptor::app()->user->getUserModel();
        if(AuthManager::checkAccess($user, 'settings.site'))
        {
            $siteLabel              = FA::icon('sitemap') . "\n" . UsniAdaptor::t('application', 'Site');
            $item                   = ['label'      => $siteLabel,
                                       'url'        => UsniAdaptor::createUrl('/settings/default/site'),
                                       'visible'    => true];
            $items[]                = $item;
        }
        if(AuthManager::checkAccess($user, 'settings.email'))
        {
            $emailLabel             = FA::icon('envelope') . "\n" . UsniAdaptor::t('users', 'Email');
            $item                   = ['label'      => $emailLabel,
                                       'url'        => UsniAdaptor::createUrl('/settings/default/email'),
                                       'visible'    => true];
            $items[]                = $item;
        }
        if(($item = UserUtil::getTopnavMenuItem($user)) != null)
        {
            $items[] = $item;
        }
        if(AuthManager::checkAccess($user, 'settings.menu'))
        {
            $menuLabel              = FA::icon('list-ul') . "\n" . UsniAdaptor::t('settings', 'Front Menu');
            $item                   = ['label'      => $menuLabel,
                                       'url'        => UsniAdaptor::createUrl('/settings/default/menu'),
                                       'visible'    => true];
            $items[]                = $item;
        }
        if(AuthManager::checkAccess($user, 'settings.menu'))
        {
            $menuLabel              = FA::icon('th-large') . "\n" . UsniAdaptor::t('settings', 'Module Settings');
            $item                   = ['label'      => $menuLabel,
                                       'url'        => UsniAdaptor::createUrl('/settings/default/module-settings'),
                                       'visible'    => true];
            $items[]                = $item;
        }
        if(AuthManager::checkAccess($user, 'settings.database'))
        {
            $menuLabel              = FA::icon('database') . "\n" . UsniAdaptor::t('settings', 'Database Settings');
            $item                   = ['label'      => $menuLabel,
                                       'url'        => UsniAdaptor::createUrl('/settings/default/database'),
                                       'visible'    => true];
            $items[]                = $item;
        }
        return $items;
    }

    /**
     * Render top nav menu for settings.
     * @return string
     */
    public static function renderTopnavMenu()
    {
        $model   = UsniAdaptor::app()->user->getUserModel();
        $content = CacheUtil::get($model->username . '-settingTopNavMenu');
        if($content === false)
        {
            $settingsItems  = static::getTopNavItems();
            if(count($settingsItems) >0 && AuthManager::checkAccess($model, 'access.settings'))
            {
                $settingsLabel = UsniAdaptor::t('settings', 'Settings');
                $headerLink    = FA::icon('cog') . "\n" .
                                 UiHtml::tag('span', $settingsLabel, ['class' => 'topnav-settings']) . "\n" .
                                 FA::icon('caret-down');
                $headerLink    = UiHtml::a($headerLink, '#', ['data-toggle' => 'dropdown', 'class' => 'dropdown-toggle']);
                $listItems     = Dropdown::widget(['items'        => $settingsItems,
                                                   'options'       => ['class' => 'dropdown-menu dropdown-menu-right'],
                                                   'encodeLabels' => false
                                                  ]);
                $content = $headerLink . $listItems;
            }
            else
            {
                $content = null;
            }
            CacheUtil::set($model->username . '-settingTopNavMenu', $content);
        }
        return $content;
    }

    /**
     * Is valid smtp info.
     * @param EmailSettingsForm $model
     * return bool
     */
    public static function isValidSmtpInfo($model)
    {
        $isValidData = true;
        if($model->sendingMethod == 'smtp')
        {
            if($model->smtpHost == null || $model->smtpPort == null || $model->smtpUsername == null || $model->smtpPassword == null)
            {
                $isValidData = false;
            }
        }
        return $isValidData;
    }
}