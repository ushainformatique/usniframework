<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;
/**
 * UserTableBuilder class file
 * @package usni\library\modules\users\managers
 */
class UserTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    {
        return [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . '(64) NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING . '(128) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . '(128) NOT NULL',
            'auth_key' => Schema::TYPE_STRING . '(128) NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'person_id' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'login_ip' => Schema::TYPE_STRING . '(20) NOT NULL',
            'last_login' => Schema::TYPE_DATETIME . ' NOT NULL',
            'timezone' => Schema::TYPE_STRING . '(32) NOT NULL',
            'type' => Schema::TYPE_STRING . '(16) NOT NULL',
        ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return[
                ['idx_status', 'status', false],
                ['idx_timezone', 'timezone', false],
                ['idx_username', 'username', true],
                ['idx_person_id', 'person_id', true]
            ];
    }
}
