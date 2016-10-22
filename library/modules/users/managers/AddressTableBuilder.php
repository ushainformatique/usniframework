<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;

/**
 * AddressTableBuilder class file
 * @package usni\library\modules\users\managers
 */
class AddressTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    { 
        return [
                'id'            => Schema::TYPE_PK,
                'address1'      => Schema::TYPE_STRING . '(128)',
                'address2'      => Schema::TYPE_STRING . '(128)',
                'city'          => Schema::TYPE_STRING . '(20)',
                'state'         => Schema::TYPE_STRING . '(20)',
                'country'       => Schema::TYPE_STRING . '(10)',
                'postal_code'   => Schema::TYPE_STRING . '(16)',
                'relatedmodel'  => Schema::TYPE_STRING . '(32) NOT NULL',
                'relatedmodel_id' => Schema::TYPE_INTEGER . '(11) NOT NULL',
                'type'            => Schema::TYPE_SMALLINT . ' NOT NULL',
                'status'          => Schema::TYPE_SMALLINT . ' NOT NULL',
            ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return [
                    ['idx_country', 'country', false],
                    ['idx_postal_code', 'postal_code', false]
               ];
    }
}