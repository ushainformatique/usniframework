<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\models;

use usni\UsniAdaptor;
use usni\library\components\UiBaseActiveRecord;
/**
 * This is the model class for table "tbl_group_members".
 * @package usni\library\modules\auth\models
 */
class GroupMember extends UiBaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $commonAttributes = ['group_id', 'member_id', 'member_type'];
        $scenarios['create'] = $scenarios['update'] = $commonAttributes;
        return $scenarios;
    }
    
	/**
     * @inheritdoc
     */
	public function rules()
	{
		return [
			[['group_id', 'member_id', 'member_type'], 'required'],
			[['group_id', 'member_id'], 'number', 'integerOnly'=>true],
			[['group_id', 'member_id', 'member_type'], 'safe'],
		];
	}

	/**
     * Get group for the member.
     * @return ActiveQuery
     */
	public function getGroup()
	{
		return $this->hasOne(Group::className(), ['id' => 'group_id']);
	}

	/**
     * @inheritdoc
     */
	public function attributeLabels()
	{
		$labels = [
                    'group_id'    => Group::getLabel(1),
                    'member_id'   => UsniAdaptor::t('auth', 'Member'),
                    'member_type' => UsniAdaptor::t('auth', 'Member Type'),
                  ];
        return parent::getTranslatedAttributeLabels($labels);
	}
}