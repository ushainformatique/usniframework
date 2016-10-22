<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\utils;

use usni\UsniAdaptor;
use usni\library\modules\auth\models\Role;
use usni\library\utils\ArrayUtil;
/**
 * Utility class for dropdowns used in auth module. It contains utility function related to dropdowns required in auth module.
 * 
 * @package usni\library\modules\auth\utils
 */
class AuthDropdownUtil
{
    /**
     * Get authorization roles.
     * @return array
     */
    public static function getRoles()
    {
        $role      = new Role();
        $rolesData = $role->getMultiLevelSelectOptions('name');
        $roles     = [];
        foreach($rolesData as $key => $value)
        {
            $roles[$value] = $value;
        }
        return $roles;
    }

    /**
     * Gets group members select data.
     * @return array
     */
    public static function getGroupMembersSelectData($modelClassNames)
    {
        $members = [];
        foreach($modelClassNames as $modelClassName)
        {
            $prefix = strtolower(UsniAdaptor::getObjectClassName($modelClassName));
            $records  = $modelClassName::find()->asArray()->all();
            $records  = ArrayUtil::map($records, 'id', 'username');
            foreach($records as $key => $value)
            {
                $members[$modelClassName::getLabel(2)][$prefix . '-' . $key] = $value;
            }
        }
        return $members;
    }
}