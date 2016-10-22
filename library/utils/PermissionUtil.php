<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\library\exceptions\MethodNotImplementedException;
use usni\library\utils\ArrayUtil;
use usni\library\components\UiSecuredActiveRecord;
use usni\UsniAdaptor;
use yii\base\Model;
use usni\library\modules\users\models\User;
use usni\library\modules\auth\managers\AuthManager;

/**
 * PermissionUtil class file.
 * 
 * @package usni\library\utils
 */
class PermissionUtil extends \yii\base\Component
{
    /**
     * Get default permissions.
     * @return array
     */
    public static function getDefaultPermissions()
    {
        return [
                    'create',
                    'view',
                    'viewother',
                    'update',
                    'updateother',
                    'delete',
                    'deleteother',
                    'manage',
                    'bulk-edit',
                    'bulk-delete'
               ];
    }

    /**
     * Get module id.
     * @throws MethodNotImplementedException
     */
    public static function getModuleId()
    {
        throw new MethodNotImplementedException();
    }

    /**
     * Get permissions.
     * @return array
     */
    public static function getPermissions()
    {
        $permissions            = [];
        $modelClassNames        = static::getModels();
        $defaultPermissions     = static::getDefaultPermissions();
        $excludedPermissions    = static::getModelToExcludedPermissions();
        //Add module access permission
        $moduleName     = ucfirst(static::getModuleId());
        $moduleResource = $moduleName . 'Module';
        $permissions[$moduleResource]['access.' . static::getModuleId()] = UsniAdaptor::t('application', 'Access Tab');
        foreach($modelClassNames as $modelClassName)
        {
            $model  = new $modelClassName();
            $modelExcludedPermissions = ArrayUtil::getValue($excludedPermissions, $modelClassName, []);
            if($model instanceof UiSecuredActiveRecord)
            {
                foreach($defaultPermissions as $permission)
                {
                    if(in_array($permission, $modelExcludedPermissions) === false)
                    {
                        $alias          = static::getPermissionAlias($modelClassName, $permission);
                        $shortModelName = UsniAdaptor::getObjectClassName($modelClassName);
                        $permission     = strtolower($shortModelName) . '.' . $permission;
                        $permissions[$shortModelName][$permission] = $alias;
                    }
                }
            }
        }
        return $permissions;
    }

    /**
     * Get models associated to the module.
     * @return array
     * @throws MethodNotImplementedException
     */
    public static function getModels()
    {
        throw new MethodNotImplementedException();
    }

    /**
     * Get model to excluded permissions.
     * @return array
     */
    public static function getModelToExcludedPermissions()
    {
         return array();
    }

    /**
     * Get permission alias.
     * @param string $modelClassName
     * @param string $permission
     * @return string
     */
    public static function getPermissionAlias($modelClassName, $permission)
    {
        if($permission == 'manage')
        {
            return self::getDefaultLabels($permission) . ' ' . $modelClassName::getLabel(2);
        }
        else
        {
            return self::getDefaultLabels($permission)  . ' ' . $modelClassName::getLabel();
        }
    }
    
    /**
     * Get default labels.
     * @param string $permission
     * @return string
     */
    public static function getDefaultLabels($permission)
    {
        switch($permission)
        {
            case 'create': return UsniAdaptor::t('application', 'Create');
            case 'update': return UsniAdaptor::t('application', 'Update');
            case 'delete': return UsniAdaptor::t('application', 'Delete');
            case 'view': return UsniAdaptor::t('application', 'View');
            case 'manage': return UsniAdaptor::t('application', 'Manage');
            case 'bulk-edit': return UsniAdaptor::t('application', 'Bulk Edit');
            case 'bulk-delete': return UsniAdaptor::t('application', 'Bulk Delete');
            case 'updateother': return UsniAdaptor::t('application', 'Update Others');
            case 'viewother': return UsniAdaptor::t('application', 'View Others');
            case 'deleteother': return UsniAdaptor::t('application', 'Delete Others');
            default: return null;
        }
    }
    
    /**
     * Checks action permission where owner is not equal to logged in user.
     * @param Model $model
     * @param User $user
     * @param string $permission
     * @return boolean
     */
    public static function doesUserHavePermissionToPerformAction($model, $user, $permission)
    {
        if($model['created_by'] != $user->id)
        {
            return AuthManager::checkAccess($user, $permission);
        }
        return true;
    }
}