<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;

/**
 * UserRoleTableBuilder class file
 * @package usni\library\modules\users\managers
 */
class UserRoleTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    {
        return [
                'username'  => Schema::TYPE_STRING . '(32) NOT NULL',
                'role'      => Schema::TYPE_STRING . '(32) NOT NULL'
            ];
    }
    
    /**
     * @inheritdoc
     */
    public function getTableName()
    {
        return '{{%user_roles}}';
    }
}