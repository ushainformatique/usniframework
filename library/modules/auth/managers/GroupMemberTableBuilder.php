<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;
/**
 * GroupMemberTableBuilder class file.
 * @package usni\library\modules\auth\managers
 */
class GroupMemberTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    {
        return [
            'group_id' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'member_id' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'member_type' => Schema::TYPE_STRING . '(16) NOT NULL',
        ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        //$this->getCommand()->addForeignKey('FK_tbl_group_members', '{{%group_member}}', 'group_id', '{{%group}}', 'id')->execute();
        return [
                  ['idx_member_type', 'member_type', false],
                  ['idx_group_member', 'group_id, member_id, member_type', true]
               ];        
    }
}
