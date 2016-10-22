<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\managers;
/**
 * TableBuilder class file.
 * @package usni\library\modules\users\managers
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
            PersonTableBuilder::className(),
            UserTableBuilder::className(),
            AddressTableBuilder::className(),
            UserMetadataTableBuilder::className(),
            UserRoleTableBuilder::className()
        ];
    }
}
