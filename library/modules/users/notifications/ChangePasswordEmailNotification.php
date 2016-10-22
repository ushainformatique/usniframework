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
/**
 * ChangePasswordEmailNotification class file.
 * 
 * @package usni\library\modules\users\notifications
 */
class ChangePasswordEmailNotification extends UiEmailNotification
{
    /**
     * User for whom password is changed
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
        return User::NOTIFY_CHANGEPASSWORD;
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
        return array('{{fullName}}' => $this->person->getFullName(),
                     '{{username}}' => $this->user->username,
                     '{{password}}' => $this->user->newPassword,
                     '{{appname}}'  => UsniAdaptor::app()->name);
    }

    /**
     * @inheritdoc
     */
    protected function getLayoutData($data)
    {
        return ['{{####content####}}' => $data['templateContent']];
    }
}