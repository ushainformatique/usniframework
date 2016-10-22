<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;

/**
 * NotificationLogsTableBuilder class file
 * @package usni\library\modules\notification\managers
 */
class NotificationLogsTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    { 
        return [
            'id'            => Schema::TYPE_PK,
            'message'       => Schema::TYPE_TEXT . ' NOT NULL',
            'type'          => Schema::TYPE_STRING . '(16) NOT NULL',
            'notification_id' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'senddatetime'  => Schema::TYPE_DATETIME . ' DEFAULT NULL',
        ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return [
                    ['idx_type', 'type', false],
                    ['idx_notification_id', 'notification_id', false]
                ];
    }
}