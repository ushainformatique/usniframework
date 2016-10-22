<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;

/**
 * NotificationTableBuilder class file
 * 
 * @package usni\library\modules\notification\managers
 */
class NotificationTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    { 
        return [
                'id'            => Schema::TYPE_PK,
                'modulename'    => Schema::TYPE_STRING . '(16) NOT NULL',
                'type'          => Schema::TYPE_STRING . '(16) NOT NULL',
                'data'          => Schema::TYPE_BINARY . ' NOT NULL',
                'status'        => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
                'priority'      => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
                'senddatetime'  => Schema::TYPE_DATETIME . ' DEFAULT NULL',
            ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return [
                    ['idx_modulename', 'modulename', false],
                    ['idx_type', 'type', false],
                    ['idx_status', 'status', false],
                    ['idx_priority', 'priority', false]
                ];
    }
}