<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;
/**
 * UserMetadataTableBuilder class file
 * @package usni\library\modules\users\managers
 */
class UserMetadataTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    {
        return [
            'id'        => Schema::TYPE_PK,
            'classname' => Schema::TYPE_STRING . '(32) NOT NULL',
            'serializeddata' => Schema::TYPE_BINARY . ' NOT NULL',
            'user_id'   => Schema::TYPE_INTEGER . '(11) NOT NULL'
        ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return [
                    ['idx_classname', 'classname', false],
                    ['idx_user_id', 'user_id', false]
               ];
    }
}
