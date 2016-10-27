<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\utils;

use usni\library\utils\PermissionUtil;
use usni\UsniAdaptor;
use usni\library\modules\auth\models\Group;
/**
 * AuthPermissionUtil class file.
 * 
 * @package usni\library\modules\auth\utils
 */
class AuthPermissionUtil extends PermissionUtil
{
    /**
     * @inheritdoc
     */
    public static function getModels()
    {
        return [
                    Group::className(),
               ];
    }

    /**
     * @inheritdoc
     */
    public static function getModuleId()
    {
        return 'auth';
    }

    /**
     * @inheritdoc
     */
    public static function getPermissions()
    {
        $permissions = parent::getPermissions();
        $permissions['AuthModule']['auth.managepermissions'] = UsniAdaptor::t('auth', 'Manage Permissions');
        return $permissions;
    }
}