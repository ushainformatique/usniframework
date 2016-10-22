<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\models;

use usni\library\components\UiBaseActiveRecord;
/**
 * UserMetdata model class.
 *
 * The followings are the available columns in table 'tbl_user_metadata':
 * @package usni\library\modules\users\models
 */
class UserMetadata extends UiBaseActiveRecord
{
	/**
     * @inheritdoc
     */
	public function rules()
	{
		return array(
			[['classname', 'serializeddata', 'user_id'], 'required'],
			[['classname', 'serializeddata', 'user_id'], 'safe', 'on'=> 'search'],
		);
	}

	/**
     * @inheritdoc
     */
	public function attributeLabels()
	{
		return array(
			'id'                => 'Id',
			'classname'         => 'Class Name',
			'serializeddata'    => 'Serialized Data',
			'user_id'           => 'User',
		);
	}
    
    /**
     * @inheritdoc
     */
    public static function getLabel($n = 1)
    {
        return null;
    }
}