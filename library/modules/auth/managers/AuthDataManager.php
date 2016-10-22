<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\managers;

use usni\library\components\UiDataManager;
use usni\library\modules\auth\models\Group;
/**
 * Loads data related to auth module.
 * 
 * @package usni\library\modules\auth\managers
 */
class AuthDataManager extends UiDataManager
{
    /**
     * @inheritdoc
     */
    public static function getModelClassName()
    {
        return Group::className();
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultDataSet()
    {
        return [
                    array('name'      => Group::getAdminGroupTitle(),
                            'parent_id' => 0,
                            'level'     => 0,
                            'status'    => Group::STATUS_ACTIVE)
                ];
    }
    
    /**
     * @inheritdoc
     */
    public static function getDefaultDemoDataSet()
    {
        return [];
    }
}