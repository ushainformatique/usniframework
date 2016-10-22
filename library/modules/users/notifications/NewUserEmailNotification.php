<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\notifications;

use usni\library\modules\users\models\User;
use usni\library\components\UiEmailNotification;
use usni\library\modules\notification\utils\NotificationUtil;
use usni\library\modules\notification\models\Notification;
use usni\UsniAdaptor;
/**
 * NewUserEmailNotification class file.
 * 
 * @package usni\library\modules\users\notifications
 */
class NewUserEmailNotification extends UiEmailNotification
{
    /**
     * User registered with the system
     * @var User 
     */
    public $user;
    
    /**
     * Person registered with the system
     * @var Person 
     */
    public $person;
    
    /**
     * @inheritdoc
     */
    public function getKey()
    {
        return User::NOTIFY_CREATEUSER;
    }

    /**
     * @inheritdoc
     */
    public function getModuleName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryPriority()
    {
        return Notification::PRIORITY_HIGH;
    }

    /**
     * @inheritdoc
     */
    protected function getTemplateData()
    {
        if($this->person->firstname == null)
        {
            $fullName = $this->user->username;
        }
        else
        {
            $fullName = $this->person->getFullName();
        }
        $confirmEmailUrl        = null;
        $confirmEmailLabel      = null;
        if($this->user->status == User::STATUS_PENDING)
        {
            $confirmEmailLabel  = UsniAdaptor::t('users', 'Please confirm your email by clicking the link below to activate your account.') . "<br/><br/>";
            $url                = $this->getConfirmEmailUrl();
            $confirmEmailUrl    = "<a href='$url'>" . UsniAdaptor::t('users', 'Confirm your email account') . "</a><br/><br/>";
        }
        return array('{{fullName}}'     => $fullName,
                     '{{username}}'     => $this->user->username,
                     '{{password}}'     => $this->user->password,
                     '{{appname}}'      => UsniAdaptor::app()->name,
                     '{{confirmemail}}' => $confirmEmailUrl,
                     '{{confirmemailLabel}}' => $confirmEmailLabel);
    }

    /**
     * @inheritdoc
     */
    protected function getLayoutData($data)
    {
        return array('{{####content####}}' => $data['templateContent']);
    }
    
    /**
     * Get confirm email url.
     * @return string
     */
    protected function getConfirmEmailUrl()
    {
        $hash               = base64_encode($this->user->password_hash);
        $email              = $this->person->email;
        $baseUrl            = NotificationUtil::getApplicationBaseUrlUsedForNotification();
        $confirmEmailUrl    = $baseUrl . $this->getValidateUrl() . '?hash=' . $hash . '&email=' . $email;
        return $confirmEmailUrl;
    }
    
    /**
     * Get validate url
     * @return string
     */
    protected function getValidateUrl()
    {
        return '/users/default/validate-email-address';
    }
}
?>