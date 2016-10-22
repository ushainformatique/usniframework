<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\utils;

use usni\library\utils\PermissionUtil;
use usni\UsniAdaptor;
use usni\library\modules\users\models\User;
use usni\library\modules\auth\managers\AuthManager;
/**
 * UsersPermissionUtil class file.
 * 
 * @package usni\library\modules\users\utils
 */
class UsersPermissionUtil extends PermissionUtil
{
    /**
     * @inheritdoc
     */
    public static function getDefaultPermissions()
    {
        $permissions = parent::getDefaultPermissions();
        $permissions[] = 'change-password';
        $permissions[] = 'change-status';
        $permissions[] = 'settings';
        $permissions[] = 'changepasswordother';
        return $permissions;
    }

    /**
     * @inheritdoc
     */
    public static function getModels()
    {
        return [User::className()];
    }

    /**
     * @inheritdoc
     */
    public static function getModuleId()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public static function getModelToExcludedPermissions()
    {
         return [User::className() => ['bulkdelete']];
    }

    /**
     * @inheritdoc
     */
    public static function getPermissionAlias($modelClassName, $permission)
    {
        if($permission == 'change-password')
        {
            return UsniAdaptor::t('users', 'Change Password');
        }
        elseif($permission == 'changepasswordother')
        {
            return UsniAdaptor::t('users', 'Change Others Password');
        }
        elseif($permission == 'change-status')
        {
            return UsniAdaptor::t('users', 'Change Status');
        }
        elseif($permission == 'settings')
        {
            return UsniAdaptor::t('settings', 'Settings');
        }
        else
        {
            return parent::getPermissionAlias($modelClassName, $permission);
        }
    }

    /**
     * @inheritdoc
     */
    public static function doesUserHavePermissionToPerformAction($model, $user, $permission)
    {
        if($model['id'] != $user->id)
        {
            if($model['created_by'] == $user->id)
            {
                return true;
            }
            return AuthManager::checkAccess($user, $permission);
        }
        return true;
    }
}