<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\managers;

use usni\library\components\UiDataManager;
use usni\library\utils\ConfigurationUtil;
use usni\library\modules\users\models\User;
use usni\library\modules\notification\utils\NotificationUtil;
use usni\UsniAdaptor;
/**
 * UsersDataManager class file.
 * 
 * @package usni\library\modules\users\managers
 */
class UsersDataManager extends UiDataManager
{
    /**
     * @inheritdoc
     */
    public static function loadDefaultData()
    {
        $installedData  = static::getUnserializedData('installdefaultdata.bin');
        $isDataLoaded   = static::checkIfClassDataLoaded($installedData);
        if($isDataLoaded)
        {
            return false;
        }
        ConfigurationUtil::insertOrUpdateConfiguration('users', 'passwordTokenExpiry', 3600);
        //Save notification template.
        $data = [
                    [
                        'type'      => 'email',
                        'notifykey' => User::NOTIFY_CREATEUSER,
                        'subject'   => UsniAdaptor::t('users', 'New User Registration'),
                        'content'   => NotificationUtil::getDefaultEmailTemplate('_newUser')
                    ],
                    [
                        'type'      => 'email',
                        'notifykey' => User::NOTIFY_CHANGEPASSWORD,
                        'subject'   => UsniAdaptor::t('users', 'You have changed your password.'),
                        'content'   => NotificationUtil::getDefaultEmailTemplate('_changePassword')
                    ],
                    [
                        'type'      => 'email',
                        'notifykey' => User::NOTIFY_FORGOTPASSWORD,
                        'subject'   => UsniAdaptor::t('users', 'Forgot Password Request'),
                        'content'   => NotificationUtil::getDefaultEmailTemplate('_forgotPassword')
                    ],
                ];
        NotificationUtil::saveNotificationTemplate($data);
        static::writeFileInCaseOfOverRiddenMethod('installdefaultdata.bin');
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public static function loadDemoData()
    {
        return;
    }
    
    /**
     * @inheritdoc
     */
    public static function getModelClassName()
    {
        return User::className();
    }
}
