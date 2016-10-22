<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\notifications;

use usni\library\components\UiEmailNotification;
use usni\library\modules\notification\models\Notification;
use usni\library\modules\users\models\User;
use usni\UsniAdaptor;
use usni\library\modules\users\utils\UserUtil;
/**
 * ForgotPasswordEmailNotification class file.
 *
 * @package usni\library\modules\users\notifications
 */
class ForgotPasswordEmailNotification extends UiEmailNotification
{
    /**
     * User registered with the system
     * @var User 
     */
    public $user;
    
    /**
     * @inheritdoc
     */
    public function getKey()
    {
        return User::NOTIFY_FORGOTPASSWORD;
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
        $password = $this->resetPassword();
        return array('{{fullName}}' => $this->user['firstname'] . ' ' . $this->user['lastname'],
                     '{{username}}' => $this->user['username'],
                     '{{password}}' => $password,
                     '{{loginUrl}}' => $this->getLoginUrl(),
                     '{{appname}}'  => UsniAdaptor::app()->name
                    );
    }

    /**
     * @inheritdoc
     */
    protected function getLayoutData($data)
    {
        return '';
    }
    
    /**
     * Reset password hash.
     * @return mixed $password.
     */
    protected function resetPassword()
    {
        $password       = UserUtil::generateRandomPassword() . UserUtil::generateSpecialChar();
        $passwordHash   = UsniAdaptor::app()->security->generatePasswordHash($password);
        $table          = $this->getTableName();
        $data           = ['password_hash' => $passwordHash];
        UsniAdaptor::app()->db->createCommand()->update($table, $data, 'id = :id', [':id' => $this->user['id']])->execute();
        return $password;
    }
    
    /**
     * Get table name
     * @return string
     */
    protected function getTableName()
    {
        return UsniAdaptor::tablePrefix() . 'user';
    }

    /**
     * Get login url
     * @return string
     */
    protected function getLoginUrl()
    {
        return UsniAdaptor::createUrl('users/default/login');
    }
}
?>