<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\service\utils;

use usni\library\utils\PermissionUtil;
use usni\UsniAdaptor;
use usni\library\utils\ButtonsUtil;
use usni\library\modules\auth\managers\AuthManager;
/**
 * ServicePermissionUtil class file.
 * 
 * @package usni\library\modules\service\utils
 */
class ServicePermissionUtil extends PermissionUtil
{
    /**
     * @inheritdoc
     */
    public static function getModels()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getModuleId()
    {
        return 'service';
    }

    /**
     * @inheritdoc
     */
    public static function getPermissions()
    {
        $permissions = array();
        $permissions['ServiceModule'] = ['access.service'  => UsniAdaptor::t('application', 'Access Tab'),
                                         'service.migrate' => UsniAdaptor::t('service', 'Run Migration'),
                                         'service.checksystem' => UsniAdaptor::t('service', 'System Configuration'),
                                         'service.loadmodulespermissions' => UsniAdaptor::t('auth', 'Rebuild Permissions'),
                                         'service.resetuserpermissions' => UsniAdaptor::t('auth', 'Reset user permissions'),
                                         'service.rebuildmodulemetadata' => UsniAdaptor::t('auth', 'Rebuild module metadata')
                                        ];
        return $permissions;
    }

    /**
     * Renders link on index page by permission.
     * @param string $label
     * @param string $url
     * @param User $user
     * @param string $permission
     * @return string
     */
    public static function renderLinkOnIndexPageByPermission($label, $url, $user, $permission)
    {
        $content = null;
        if(AuthManager::checkAccess($user, $permission))
        {
            $content = "<tr>
                            <td>" . $label . "</td>
                            <td style='width: 30%'>" . ButtonsUtil::getRunButton($url) . "</td>
                        </tr>";
        }
        return $content;
    }
    
    /**
     * @inheritdoc
     */
    public static function doesUserHavePermissionToPerformAction($model, $user, $permission)
    {
        return AuthManager::checkAccess($user, $permission);
    }
}