<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;

/**
 * NotificationTemplateTableBuilder class file
 * @package usni\library\modules\notification\managers
 */
class NotificationTemplateTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    {  
        return [
                'id'            => Schema::TYPE_PK,
                'type'          => Schema::TYPE_STRING . '(10) NOT NULL',
                'notifykey'     => Schema::TYPE_STRING . '(32) NOT NULL',
                'layout_id'     => Schema::TYPE_INTEGER . '(11) DEFAULT NULL',
                'status'        => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
            ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return [
                    ['idx_notifykey', 'notifykey', false],
                    ['idx_type', 'type', false],
                    ['idx_status', 'status', false]
                ];
    }
    
    /**
     * @inheritdoc
     */
    protected static function isTranslatable()
    {
        return true;
    }
}