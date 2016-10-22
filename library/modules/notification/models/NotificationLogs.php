<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\models;

use usni\library\components\UiBaseActiveRecord;
use usni\UsniAdaptor;
/**
 * This is the model class for table "tbl_notification_logs".
 * 
 * @package usni\library\modules\notification\models
 */
class NotificationLogs extends UiBaseActiveRecord
{
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
        return [
            [['message', 'senddatetime', 'type', 'notification_id'], 'required'],
            ['type',     'integer'],
            ['notification_id', 'integer'],
            [['id', 'message', 'senddatetime', 'type', 'notification_id'], 'safe']
        ];
	}

	/**
     * @inheritdoc
     */
	public function attributeLabels()
	{
		return [
                    'id'            => UsniAdaptor::t('application',  'Id'),
                    'message'       => UsniAdaptor::t('notification', 'Message'),
                    'senddatetime'  => UsniAdaptor::t('notification', 'Send Date Time'),
                    'type'          => UsniAdaptor::t('application', 'Type'),
               ];
	}

	/**
     * Should add created and modified fields.
     * @return boolean
     */
    public function shouldAddCreatedAndModifiedFields()
    {
        return false;
    }
}