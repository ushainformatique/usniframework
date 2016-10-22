<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\models;

use usni\library\components\UiFormModel;
use usni\UsniAdaptor;
use usni\library\validators\UiEmailValidator;
use usni\library\modules\settings\notifications\TestMessageEmailNotification;
use usni\library\modules\notification\utils\NotificationUtil;
use usni\library\modules\notification\models\Notification;
/**
 * EmailSettingsForm class file.
 * 
 * @package usni\library\modules\settings\models
 */
class EmailSettingsForm extends UiFormModel
{
    public $fromName;

    public $fromAddress;

    public $replyToAddress;

    public $sendingMethod;

    //public $pathToSendmail;

    public $testEmailAddress;

    public $sendTestMail;

    public $smtpHost;

    public $smtpPort;

    public $smtpUsername;

    public $smtpPassword;

    public $smtpAuth;
    
    public $testMode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                    [['fromName', 'fromAddress', 'replyToAddress', 'sendingMethod'],                            'required'],
                    [['testEmailAddress', 'smtpHost', 'smtpPort', 'smtpUsername', 'smtpPassword', 'smtpAuth', 'testMode'],  'safe'],
                    [['sendTestMail', 'smtpAuth'],                                                              'default',      'value' => 0],
                    ['fromName',                                                                                'string'],
                    [['fromAddress', 'replyToAddress', 'testEmailAddress'],                                      UiEmailValidator::className()],
               ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
                    'fromName'              => UsniAdaptor::t('notification', 'From Name'),
                    'fromAddress'           => UsniAdaptor::t('notification', 'From Address'),
                    'replyToAddress'        => UsniAdaptor::t('notification', 'Reply To Address'),
                    'testEmailAddress'      => UsniAdaptor::t('notification', 'Test Email Address'),
                    'sendingMethod'         => UsniAdaptor::t('notification', 'Sending Method'),
                    //'pathToSendmail'        => UsniAdaptor::t('notification', 'pathToSendmail'),
                    'smtpHost'              => UsniAdaptor::t('notification', 'SMTP Host'),
                    'smtpPort'              => UsniAdaptor::t('notification', 'SMTP Port'),
                    'smtpUsername'          => UsniAdaptor::t('notification', 'SMTP Username'),
                    'smtpPassword'          => UsniAdaptor::t('notification', 'SMTP Password'),
                    'smtpAuth'              => UsniAdaptor::t('notification', 'Use SMTP Authentication'),
                    'testMode'              => UsniAdaptor::t('notification', 'Enable test mode')
               ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
                    'fromName'              => UsniAdaptor::t('notificationhint', 'Default from name of the system user sending the email.'),
                    'fromAddress'           => UsniAdaptor::t('notificationhint', 'Default from address of the system user sending the email.'),
                    'replyToAddress'        => UsniAdaptor::t('notificationhint', 'Default reply to address in the system.'),
                    'testEmailAddress'      => UsniAdaptor::t('notificationhint', 'Test email address'),
                    'sendingMethod'         => UsniAdaptor::t('notificationhint', 'Sending Method'),
                    //'pathToSendmail'        => t('notification', 'pathToSendmail'),
                    'smtpHost'              => UsniAdaptor::t('notificationhint', 'SMTP Host'),
                    'smtpPort'              => UsniAdaptor::t('notificationhint', 'SMTP Port'),
                    'smtpUsername'          => UsniAdaptor::t('notificationhint', 'SMTP Username'),
                    'smtpPassword'          => UsniAdaptor::t('notificationhint', 'SMTP Password'),
                    'smtpAuth'              => UsniAdaptor::t('notificationhint', 'Use SMTP Authentication'),
                    'testMode'              => UsniAdaptor::t('notification', 'Enable test mode where notification data would be stored in log tables in database.')
               ];
    }
    
    /**
     * @inheritdoc
     */
    public static function getLabel($n = 1)
    {
        return UsniAdaptor::t('settings', 'Email Settings');
    }
    
    /**
     * Sends test email
     * @return void
     */
    public function sendTestMail()
    {
        $emailNotification  = new TestMessageEmailNotification();
        $mailer             = UsniAdaptor::app()->mailer;
        $mailer->emailNotification = $emailNotification;
        $message            = $mailer->compose();
        list($fromName, $fromAddress) = NotificationUtil::getSystemFromAddressData();
        $isSent = $message->setFrom([$fromAddress => $fromName])
                ->setTo($this->testEmailAddress)
                ->setSubject($emailNotification->getSubject())
                ->send();
        $data               = serialize(array(
                                'fromName'    => $fromName,
                                'fromAddress' => $fromAddress,
                                'toAddress'   => $this->testEmailAddress,
                                'subject'     => $emailNotification->getSubject(),
                                'body'        => $message->toString()));
        $status             = $isSent === true ? Notification::STATUS_SENT : Notification::STATUS_PENDING;
        //Save notification
        return NotificationUtil::saveEmailNotification($emailNotification, $status, $data);
    }
}