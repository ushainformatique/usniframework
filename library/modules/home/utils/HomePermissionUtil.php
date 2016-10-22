<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\home\utils;

use usni\library\utils\PermissionUtil;
/**
 * HomePermissionUtil class file.
 * 
 * @package usni\library\modules\home\utils
 */
class HomePermissionUtil extends PermissionUtil
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
        return 'home';
    }

    /**
     * @inheritdoc
     */
    public static function getPermissions()
    {
        return [];
    }
}