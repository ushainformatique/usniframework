<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;
/**
 * AuthAssignmentTableBuilder class file.
 * @package usni\library\modules\auth\managers
 */
class AuthAssignmentTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    {
        return [
                    'identity_name' => Schema::TYPE_STRING . '(32) NOT NULL',
                    'identity_type' => Schema::TYPE_STRING . '(16) NOT NULL',
                    'permission' => Schema::TYPE_STRING . '(64) NOT NULL',
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
                    ['idx_identity_name',   'identity_name', false],
                    ['idx_identity_type', 'identity_type', false],
                    ['idx_permission', 'permission', false]
            ];
    }
}
?>