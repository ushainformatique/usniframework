<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\models;

use usni\library\components\TranslatableActiveRecord;
use usni\UsniAdaptor;
use usni\library\modules\notification\models\NotificationLayout;
/**
 * NotificationTemplate active record.
 *
 * @package usni\library\modules\notification\models
 */
class NotificationTemplate extends TranslatableActiveRecord
{
	/**
     * @inheritdoc
     */
	public function rules()
	{
		return [
                    [['type', 'notifykey', 'subject', 'content'],                   'required', 'except' => 'bulkedit'],
                    [['notifykey'],  'unique', 'targetAttribute' => ['notifykey', 'type'], 'on' => 'create'],
                    ['notifykey',    'unique', 'targetAttribute' => ['notifykey', 'type'], 'filter' => ['!=', 'id', $this->id], 'on' => 'update'],
                    [['subject', 'content', 'type', 'notifykey', 'layout_id'],      'safe'],
               ];
	}
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenario               = parent::scenarios();
        $scenario['create']     = ['type', 'notifykey', 'subject', 'content', 'layout_id'];
        $scenario['update']     = ['type', 'notifykey', 'subject', 'content', 'layout_id'];
        $scenario['bulkedit']   = ['type'];
        return $scenario;
    }
    
     /**
     * Get Notification Layout for the Template.
     * @return \NotificationLayout
     */
    public function getLayout()
    {
        return $this->hasOne(NotificationLayout::className(), ['id' => 'layout_id']);
    }
    
    /**
     * @inheritdoc
     */
	public function attributeLabels()
	{
		$labels = [
                                'id'          => UsniAdaptor::t('application',  'Id'),
                                'notifykey'   => UsniAdaptor::t('notification', 'Notify Key'),
                                'type'        => UsniAdaptor::t('notification', 'Type'),
                                'subject'     => UsniAdaptor::t('application', 'Subject'),
                                'content'     => UsniAdaptor::t('notification', 'Content'),
                                'layout_id'   => UsniAdaptor::t('notification', 'Notification Layout')
                          ];
        return parent::getTranslatedAttributeLabels($labels);
	}

    /**
     * @inheritdoc
     */
    public static function getLabel($n=1)
    {
        return ($n == 1) ? UsniAdaptor::t('notification', 'Template') : UsniAdaptor::t('notification', 'Templates');
    }

    /**
     * Gets available notification types.
     * @return array
     */
    public static function getNotificationType()
    {
        return ['email' => UsniAdaptor::t('users', 'Email')];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
                    'subject'   => UsniAdaptor::t('notificationhint', 'Subject for the email'),
                    'notifykey' => UsniAdaptor::t('notificationhint', 'Notification Key'),
                    'type'      => UsniAdaptor::t('notificationhint', 'Type of notification'),
               ];
    }
    
    /**
     * @inheritdoc
     */
    public static function getTranslatableAttributes()
    {
        return ['subject', 'content'];
    }
    
    /**
     * Gets layout name.
     * @param mixed $data the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]].
     * @param DataColumn $column
     * @return string
     */
    public function getNotificationLayoutName($data, $key, $index, $column)
    {
        $layout = NotificationLayout::findOne($data->layout_id);
        if(!empty($layout))
        {
            return $layout->name;
        }
        return UsniAdaptor::t('application', '(not set)');
    }
}
?>