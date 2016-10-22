<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */

namespace usni\library\modules\notification\models;

use usni\library\components\UiSecuredActiveRecord;
use usni\UsniAdaptor;
use usni\library\modules\notification\utils\NotificationUtil;
use usni\library\utils\ArrayUtil;
use usni\library\utils\DateTimeUtil;
/**
 * Notification active record.
 *
 * @package usni\library\modules\notification\models
 */
class Notification extends UiSecuredActiveRecord
{
    /**
     * Constant for Notification mail type.
     */
    const TYPE_EMAIL       = 1;

    /**
     * Constant for Notification normal priority.
     * @see class.phpmailer.com $Priority
     */
    const PRIORITY_NORMAL  = 3;

    /**
     * Constant for Notification low priority.
     */
    const PRIORITY_LOW     = 5;

    /**
     * Constant for Notification high priority.
     */
    const PRIORITY_HIGH    = 1;

    /**
     * Constant for Notification pending status.
     */
    const STATUS_PENDING   = 0;

    /**
     * Constant for Notification sent status.
     */
    const STATUS_SENT      = 1;

    /**
     * Start date used for search. @see NotificationSearchView
     * @var string
     */
    public $startDate;

    /**
     * End date used for search. @see NotificationSearchView
     * @var string
     */
    public $endDate;

    /**
     * Start date hidden used for search. @see NotificationSearchView
     * @var string
     */
    public $startDateHidden;

    /**
     * End date hidden used for search. @see NotificationSearchView
     * @var string
     */
    public $endDateHidden;

	/**
     * @inheritdoc
     */
	public function rules()
	{
		return [
                    [['modulename', 'type', 'data'],        'required'],
                    [['priority', 'type'],                  'integer'],
                    ['status',                              'default', 'value' => Notification::STATUS_PENDING],
                    [['modulename'],                        'string', 'max'=>16],
                    ['startDate',                           'string'],
                    ['endDate',                             'string'],
                    [['modulename', 'type', 'data', 'status', 'priority', 'senddatetime', 'startDate', 'endDate', 'startDateHidden', 
                      'endDateHidden'], 'safe'],
               ];
	}

	/**
     * @inheritdoc
     */
	public function attributeLabels()
	{
        $labels = [
                        'id'            => UsniAdaptor::t('application', 'Id'),
                        'modulename'    => UsniAdaptor::t('notification','Module Name'),
                        'type'          => UsniAdaptor::t('notification','Type'),
                        'data'          => UsniAdaptor::t('notification','Data'),
                        'status'        => UsniAdaptor::t('notification','Status'),
                        'priority'      => UsniAdaptor::t('notification','Priority'),
                        'senddatetime'  => UsniAdaptor::t('notification','Send Date Time'),
                        'startDate'     => UsniAdaptor::t('notification','Start Date'),
                        'endDate'       => UsniAdaptor::t('notification','End Date')
                  ];
        return parent::getTranslatedAttributeLabels($labels);
	}

    /**
     * @inheritdoc
     */
    public static function getLabel($n = 1)
    {
        return ($n == 1) ? UsniAdaptor::t('notification', 'Notification') : UsniAdaptor::t('users', 'Notifications');
    }

   /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [];
    }

    /**
     * Get unserialize data for notification.
     * @param @param ActiveRecord $data
     * @return string
     */
    public function getNotificationMessage($data, $key, $index, $column)
    {
        $content  = '';
        $message  = unserialize($data->data);
        foreach ($message as $key => $value)
        {
            if(is_array($value))
            {
                $content .= $key. str_repeat('&nbsp;', 5) . implode(',', $value) . '<br/>';
            }
            else
            {
                $content .= $key. str_repeat('&nbsp;', 5) . $value  . '<br/>';
            }
        }
        return $content;
    }
    
    /**
     * Gets type display label.
     * @param mixed $data the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]].
     * @param DataColumn $column
     * @return string
     */
    public function getTypeDisplayLabel($data, $key, $index, $column)
    {
        $typeLabelData = NotificationUtil::getTypes();
        if(($label = ArrayUtil::getValue($typeLabelData, $data->type)) !== null)
        {
            return $label;
        }
        return UsniAdaptor::t('application', '(not set)');
    }
    
    /**
     * Gets send date time.
     * @param mixed $data the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]].
     * @param DataColumn $column
     * @return string
     */
    public function getSendDateTime($data, $key, $index, $column)
    {
        return DateTimeUtil::getFormattedDateTime($data->senddatetime);
    }
}