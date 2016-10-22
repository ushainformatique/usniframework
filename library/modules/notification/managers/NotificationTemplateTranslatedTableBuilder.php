<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;

/**
 * NotificationTemplateTranslatedTableBuilder class file
 * @package usni\library\modules\notification\managers
 */
class NotificationTemplateTranslatedTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    { 
        return [
                'id'            => Schema::TYPE_PK,
                'owner_id'      => Schema::TYPE_INTEGER . '(11) NOT NULL',
                'language'      => Schema::TYPE_STRING . '(10) NOT NULL',
                'subject'       => Schema::TYPE_STRING . '(128) NOT NULL',
                'content'       => Schema::TYPE_BINARY . ' NOT NULL'
            ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return [
                    ['idx_subject', 'subject', false],
                    ['idx_owner_id', 'owner_id', false],
                    ['idx_language', 'language', false]
                ];
    }
}