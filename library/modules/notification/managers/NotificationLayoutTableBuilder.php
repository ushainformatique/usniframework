<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;

/**
 * NotificationLayoutTableBuilder class file
 * @package usni\library\modules\notification\managers
 */
class NotificationLayoutTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    { 
          return [
                'id'            => Schema::TYPE_PK,
                'status'        => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
            ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return [['idx_status', 'status', false]];
    }
    
    /**
     * @inheritdoc
     */
    protected static function isTranslatable()
    {
        return true;
    }
}