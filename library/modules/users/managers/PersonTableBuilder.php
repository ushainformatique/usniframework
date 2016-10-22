<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;

/**
 * PersonTableBuilder class file
 * @package usni\library\modules\users\managers
 */
class PersonTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    { 
        return [
                'id'            => Schema::TYPE_PK,
                'firstname'     => Schema::TYPE_STRING . '(32) NOT NULL',
                'lastname'      => Schema::TYPE_STRING . '(32) NOT NULL',
                'mobilephone'   => Schema::TYPE_STRING . '(16)',
                'officephone'   => Schema::TYPE_STRING . '(16)',
                'officefax'     => Schema::TYPE_STRING . '(16)',
                'email'         => Schema::TYPE_STRING . '(64)',
                'avatar'        => Schema::TYPE_STRING . '(128)',
                'profile_image' => Schema::TYPE_STRING . '(255)'
            ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return [
                    ['idx_email', 'email', true]
               ];
    }
}
