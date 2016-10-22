<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;
/**
 * RoleTableBuilder class file.
 * 
 * @package usni\library\modules\auth\managers
 */
class RoleTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    {
        return [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . '(128) NOT NULL',
            'parent_id' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'level' => Schema::TYPE_INTEGER . '(1) NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL',
        ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return [
                    ['idx_level', 'level', false],
                    ['idx_status', 'status', false]
                ];
    }
}