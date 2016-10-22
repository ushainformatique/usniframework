<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;
/**
 * AuthPermissionTableBuilder class file.
 * 
 * @package usni\library\modules\auth\managers
 */
class AuthPermissionTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    {
        return [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'alias' => Schema::TYPE_STRING . '(64) NOT NULL',
            'resource' => Schema::TYPE_STRING . '(32) NOT NULL',
            'module' => Schema::TYPE_STRING . '(32) NOT NULL',
        ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return [
                    ['idx_name', 'name', false],
                    ['idx_alias', 'alias', false],
                    ['idx_resource', 'resource', false],
                    ['idx_module', 'module', false],
                    ['idx_permission', 'name, module, resource, alias', true],
                ];
    }
}
