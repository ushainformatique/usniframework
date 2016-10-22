<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\utils;

use usni\library\modules\notification\models\NotificationLogs;
use usni\library\modules\notification\models\Notification;
use usni\UsniAdaptor;
use usni\library\utils\ArrayUtil;
use usni\library\components\UiHtml;
use usni\library\exceptions\FailedToSaveModelException;
use usni\library\utils\ConfigurationUtil;
use Yii;
use yii\helpers\Url;
use usni\library\extensions\bootstrap\widgets\UiLabel;
use usni\library\modules\notification\models\NotificationLayout;
use usni\library\utils\AdminUtil;
use usni\library\modules\notification\models\NotificationTemplate;
use usni\library\modules\users\utils\UserUtil;
use usni\library\modules\users\models\User;
/**
 * Contains helper functions for notifications.
 * 
 * @package usni\library\modules\notification\utils
 */
class NotificationUtil
{
    /**
     * Active status constant.
     */
    const STATUS_SENT = 1;
    /**
     * Pending status constant.
     */
    const STATUS_PENDING    = 0;
    
    /**
     * Check if notification template exists for the key.
     * @param string $key Notification key.
     * @return boolean
     */
    public static function getNotificationTemplate($key)
    {
        $notificationTemplateModel = NotificationTemplate::findByAttribute('notifykey', $key);
        if ($notificationTemplateModel != null)
        {
            return $notificationTemplateModel;
        }
        else
        {
            //Yii::log() method does not exist in yii2
            Yii::trace(UsniAdaptor::t('notification', 'The notification template is missing for key: {key}', array('{key}' => $key)), \yii\log\Logger::LEVEL_ERROR);
            return null;
        }
    }

    /**
     * Gets type display label.
     * @param integer $type
     * @return string
     */
    public static function getTypeDisplayLabel($type)
    {
        $typeLabelData = NotificationUtil::getTypes();
        if(($label = ArrayUtil::getValue($typeLabelData, $type)) !== null)
        {
            return $label;
        }
        return UsniAdaptor::t('application', '(not set)');
    }

    /**
     * Gets status display label.
     * @param integer $status
     * @return string
     */
    public static function getStatusDisplayLabel($status)
    {
        $statusLabelData = NotificationUtil::getStatusListData();
        if(($label = ArrayUtil::getValue($statusLabelData, $status)) !== null)
        {
            return $label;
        }
        return UsniAdaptor::t('application', '(not set)');
    }

    /**
     * Gets priority display label.
     * @param integer $status
     * @return string
     */
    public static function getPriorityDisplayLabel($status)
    {
        $priorityLabelData = NotificationUtil::getPriorityListData();
        if(($label = ArrayUtil::getValue($priorityLabelData, $status)) !== null)
        {
            return $label;
        }
        return UsniAdaptor::t('application', '(not set)');
    }

    /**
     * Get status for notifications.
     * @return array
     */
    public static function getStatusListData()
    {
        return array(
            Notification::STATUS_SENT       => UsniAdaptor::t('application', 'Sent'),
            Notification::STATUS_PENDING    => UsniAdaptor::t('application', 'Pending')
        );
    }
    
    /**
     * Renders label for the status.
     * @param string $data ActiveRecord of the model.
     * @return string
     */
    public static function renderLabel($data)
    {
        $value      = self::getLabel($data);
        if ($value == UsniAdaptor::t('application', 'Sent'))
        {
            return UiLabel::widget(['content' => $value, 'modifier' => UiHtml::COLOR_SUCCESS]);
        }
        elseif ($value == UsniAdaptor::t('application', 'Pending'))
        {
            return UiLabel::widget(['content' => $value, 'modifier' => UiHtml::COLOR_DANGER]);
        }
    }
    
    /**
     * Gets label for the status.
     * @param string $data ActiveRecord of the model.
     * @return string
     */
    public static function getLabel($data)
    {
        if(is_array($data))
        {
            $data = (object)$data;
        }
        if ($data->status == self::STATUS_SENT)
        {
            return UsniAdaptor::t('application', 'Sent');
        }
        else if ($data->status == self::STATUS_PENDING)
        {
            return UsniAdaptor::t('application', 'Pending');
        }
    }

    /**
     * Get priority for notifications.
     * @return array
     */
    public static function getPriorityListData()
    {
        return array(
            Notification::PRIORITY_LOW      => UsniAdaptor::t('notification', 'Low'),
            Notification::PRIORITY_NORMAL   => UsniAdaptor::t('notification', 'Medium'),
            Notification::PRIORITY_HIGH     => UsniAdaptor::t('notification', 'High')
        );
    }

    /**
     * Gets charset list data.
     * @return array
     */
    public static function getCharsetListData()
    {
        return array('iso-8859-1' => 'iso-8859-1',
                     'UTF-8'      => 'UTF-8');
    }

    /**
     * Gets content type list data.
     * @return array
     */
    public static function getContentType()
    {
        return array('text/plain' => 'text/plain',
                     'text/html'  => 'text/html');
    }

    /**
     * Gets encoding list data.
     * @return array
     */
    public static function getEncoding()
    {
        return array('8bit'             => '8bit',
                     '7bit'             => '7bit',
                     'binary'           => 'binary',
                     'base64'           => 'base64',
                     'quoted-printable' => 'quoted-printable');
    }

    /**
     * Get types of notifications.
     * @return array
     */
    public static function getTypes()
    {
        return array(
            Notification::TYPE_EMAIL => UsniAdaptor::t('users', 'Email')
        );
    }

    /**
     * Get Sending method of mail. eg. simple|SMTP
     */
    public static function getMailSendingMethod()
    {
        return array('mail'     => UsniAdaptor::t('notification', 'Mail'),
                     //'sendmail' => UsniAdaptor::getLabel('notification', 'sendmail'),
                     'smtp'     => UsniAdaptor::t('notification', 'SMTP'));
    }

    /**
     * Registers notification preview script.
     * @param string $url
     * @param string $editViewId
     * @see http://stackoverflow.com/questions/24398225/ckeditor-doesnt-submit-data-via-ajax-on-first-submit
     * @return void
     */
    public static function registerPreviewScript($url, $editViewId, $view)
    {
        $editViewId = strtolower($editViewId);
        $editViewId = UsniAdaptor::getObjectClassName($editViewId);
        $view->registerJs("
                            $('body').on('click', '#preview-button',
                            function()
                            {
                              var data = $('#$editViewId').serialize();
                              $.ajax({
                                 'type' : 'post',
                                 'url'  : '{$url}',
                                 'data' : data,
                                 'beforeSend' : function()
                                                {
                                                  attachButtonLoader($('#$editViewId'));
                                                },
                                 'success'    : function(data)
                                                {
                                                  $('.modal-body').html(data);
                                                  $('#previewModal').modal('show');
                                                  removeButtonLoader($('#$editViewId'));
                                                }
                              });
                              return false;
                             });
                          ", \yii\web\View::POS_END);
    }

    /**
     * Get application base url used for notification.
     * @return string
     * @see NewUserEmailNotification
     */
    public static function getApplicationBaseUrlUsedForNotification()
    {
        $baseUrl = Url::base(true);
        if(UsniAdaptor::app()->urlManager->showScriptName == true)
        {
            $baseUrl .= '/index.php';
        }
        return $baseUrl;
    }
    
    /**
     * Save logs.
     * @param UiEmailNotification $emailNotification
     * @param yii\swiftmailer\Message $message
     * @return Object
     */
    public static function saveLogs($emailNotification, $message)
    {
        $notification                       = $emailNotification->notification;
        $notificationLogs                   = new NotificationLogs();
        $notificationLogs->message          = $message->toString();
        $notificationLogs->type             = Notification::TYPE_EMAIL;
        $notificationLogs->senddatetime     = date('Y-m-d H:i:s');
        $notificationLogs->notification_id  = $notification->id;
        if($notificationLogs->save())
        {
            return true;
        }
        else
        {
            throw new FailedToSaveModelException(NotificationLogs::className());
        }
    }
    
    /**
     * Get email settings from config.
     * @return array
     */
    public static function getEmailSettingsFromConfig()
    {
        $emailSettings = ConfigurationUtil::getValue('settings', 'emailSettings');
        if($emailSettings != null)
        {
            return unserialize($emailSettings);
        }
        return [];
    }
    
    /**
     * Gets status dropdown for post.
     * @return array
     */
    public static function getNotificationTypeSelectOptions()
    {
        return [
                    'email' => UsniAdaptor::t('application', 'Email'),
               ];
    }
    
    /**
     * Gets template select options.
     * @param User $user
     * @return array
     */
    public static function getLayoutSelectOptions()
    {
        return AdminUtil::getTranslatableModelSelectOptions(NotificationLayout::className());
    }
    
    /**
     * Registers notification grid preview script.
     * @param string $url
     * @param string $editViewId
     * @param \yii\web\View $view
     * @see http://stackoverflow.com/questions/24398225/ckeditor-doesnt-submit-data-via-ajax-on-first-submit
     * @return void
     */
    public static function registerGridPreviewScript($url, $editViewId, $view)
    {
        $view->registerJs("
                            $('body').on('click', '.grid-preview-link',
                            function()
                            {
                              var id = $(this).data('id');
                              $.ajax({
                                 'type' : 'post',
                                 'url'  : '{$url}' + '?id=' + id,
                                 'beforeSend' : function()
                                                {
                                                  attachButtonLoader($('#$editViewId'));
                                                },
                                 'success'    : function(data)
                                                {
                                                  $('#gridPreviewModal').find('.modal-body').html(data);
                                                  $('#gridPreviewModal').modal('show');
                                                  removeButtonLoader($('#$editViewId'));
                                                }
                              });
                              return false;
                             });
                          ", \yii\web\View::POS_END);
    }
    
    /**
     * Get default data template
     * @param string $template
     * @return string
     */
    public static function getDefaultEmailTemplate($template)
    {
        $rawLanguage       = UsniAdaptor::app()->languageManager->getLanguageWithoutLocale();
        return UsniAdaptor::app()->getView()->renderFile('@usni/library/modules/notification/email/' . $rawLanguage . '/' . $template . '.php');
    }
    
    /**
     * Get system from address data. It would be used as default from address data
     * @return array
     */
    public static function getSystemFromAddressData()
    {
        $email  = null;
        $name   = null;
        $data   = ConfigurationUtil::getValue('settings', 'emailSettings');
        if($data != null)
        {
            $settings = unserialize($data);
            $email    = ArrayUtil::getValue($settings, 'fromAddress', null);
            $name     = ArrayUtil::getValue($settings, 'fromName', null);
        }
        $super  = UserUtil::getUserById(User::SUPER_USER_ID);
        if($email == null)
        {
            $email = $super['email'];
        }
        if($name == null)
        {
            $name = $super['firstname'] . ' ' . $super['lastname'];
        }
        return [$name, $email];
    }
    
    /**
     * Save logs.
     * @param UiEmailNotification $emailNotification
     * @param int $status
     * @param string $data
     * @return boolean
     */
    public static function saveEmailNotification($emailNotification, $status, $data)
    {
        $tableName      = UsniAdaptor::app()->db->tablePrefix . 'notification';
        $columns        = [ 'modulename' => $emailNotification->getModuleName(), 
                            'type' => Notification::TYPE_EMAIL, 
                            'data' => $data, 
                            'priority' => $emailNotification->getDeliveryPriority(), 
                            'status' => $status,
                            'senddatetime' => date('Y-m-d H:i:s'), 
                            'created_by' => User::SUPER_USER_ID, 
                            'created_datetime' => date('Y-m-d H:i:s')];
        return UsniAdaptor::app()->db->createCommand()->insert($tableName, $columns)->execute();
    }
    
    /**
     * Save notification template.
     * @param array $data
     * @throws FailedToSaveModelException
     */
    public static function saveNotificationTemplate($data)
    {
        foreach ($data as $key => $val)
        {
            $model = new NotificationTemplate(['scenario' => 'create']);
            foreach ($val as $attribute => $value)
            {
                $model->$attribute = $value;
            }
            if(!$model->save())
            {
                throw new \usni\library\exceptions\FailedToSaveModelException(get_class($model));
            }
        }
    }
}