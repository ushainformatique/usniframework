<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\models;

use yii\base\Model;
use usni\UsniAdaptor;
use usni\library\modules\notification\utils\NotificationUtil;
use usni\library\modules\notification\models\Notification;
use usni\library\modules\users\notifications\ChangePasswordEmailNotification;

/**
 * Change password form model
 *
 * @package usni\library\modules\users\models
 */
class ChangePasswordForm extends Model
{
    /**
     * New password to be set.
     * @var string
     */
    public $newPassword;

    /**
     * Confirm passoword against new password.
     * @var string
     */
    public $confirmPassword;

    /**
     * User associated.
     * @var Model
     */
    public $user;
    
    /**
     * Person associated
     * @var Person 
     */
    public $person;

    /**
     * Validation rules for the model.
     * @return array Validation rules for model attributes.
     */
    public function rules()
    {
        return [
            [['newPassword','confirmPassword'], 'required'],
            [['confirmPassword'], 'compare', 'compareAttribute' => 'newPassword'],
            ['newPassword', 'match', 'pattern' => '/^((?=.*\d)(?=.*[a-zA-Z])(?=.*\W).{6,20})$/i'],
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $this->user->setPasswordHash($this->newPassword);
        $this->user->save();
        //Assigning so that it could be picked in notification
        $this->user->newPassword = $this->newPassword;
        //Assigning person so that another query is not hit while sending email
        $this->person = $this->user->person;
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function getLabel()
    {
        return UsniAdaptor::t('users', 'Change Password');
    }
    
    /**
     * Get attribute hints.
     * return array
     */
    public function attributeHints()
    {
        return array(
             'newPassword' => UsniAdaptor::t('userhint', 'Must be of 6-20 characters. Contains atleast one special, one numeric & one alphabet.'),
             'confirmPassword' => UsniAdaptor::t('userhint', 'Must be of 6-20 characters. Contains atleast one special, one numeric & one alphabet.')
        );
    }
    
    /**
     * Sends change password email
     * @return boolean
     */
    public function sendMail()
    {
        $mailer             = UsniAdaptor::app()->mailer;
        $emailNotification  = $this->getEmailNotification();
        $mailer->emailNotification = $emailNotification;
        $message            = $mailer->compose();
        $toAddress          = $this->person->email;
        list($fromName, $fromAddress) = NotificationUtil::getSystemFromAddressData();
        $isSent             = $message->setFrom([$fromAddress => $fromName])
                            ->setTo($toAddress)
                            ->setSubject($emailNotification->getSubject())
                            ->send();
        $data               = serialize(array(
                                'fromName'    => $fromName,
                                'fromAddress' => $fromAddress,
                                'toAddress'   => $toAddress,
                                'subject'     => $emailNotification->getSubject(),
                                'body'        => $message->toString()));
        $status             = $isSent === true ? Notification::STATUS_SENT : Notification::STATUS_PENDING;
        //Save notification
        return NotificationUtil::saveEmailNotification($emailNotification, $status, $data);
    }
    
    /**
     * Get email notification
     * @return NewUserEmailNotification
     */
    protected function getEmailNotification()
    {
        return new ChangePasswordEmailNotification(['user' => $this->user, 'person' => $this->person]);
    }
}