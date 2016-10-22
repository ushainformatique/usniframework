<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\managers;
/**
 * TableBuilder class file.
 * @package usni\library\modules\auth\managers
 */
class TableManager extends \usni\library\components\UiTableManager
{
    /**
     * Get table builder config.
     * @return array
     */
    protected static function getTableBuilderConfig()
    {
        return [
                    AuthAssignmentTableBuilder::className(),
                    AuthPermissionTableBuilder::className(),
                    GroupTableBuilder::className(),
                    GroupMemberTableBuilder::className(),
                    RoleTableBuilder::className()        
                ];
    }
}
