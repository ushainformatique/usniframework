<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;

/**
 * ConfigurationTableBuilder class file
 * @package usni\library\managers
 */
class ConfigurationTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    { 
        return [
                'id'            => Schema::TYPE_PK,
                'module'        => Schema::TYPE_STRING . '(32) NOT NULL',
                'key'           => Schema::TYPE_STRING . '(32) NOT NULL',
                'value'         => Schema::TYPE_TEXT . ' NOT NULL'
            ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return [
                    ['idx_module', 'module', false],
                    ['idx_key', 'key', false]
               ];
    }
}