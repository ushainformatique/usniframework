<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\utils;

use usni\UsniAdaptor;
use usni\library\modules\users\models\User;
use usni\library\modules\users\models\Person;
use usni\library\modules\users\models\Address;
use usni\library\components\UiHtml;
use usni\library\modules\auth\managers\AuthManager;
use usni\fontawesome\FA;
use yii\bootstrap\Dropdown;
use usni\library\utils\TimezoneUtil;
use usni\library\utils\ArrayUtil;
use usni\library\modules\users\models\UserEditForm;
use usni\library\modules\auth\models\GroupMember;
use usni\library\utils\CacheUtil;
use usni\library\modules\users\models\ChangePasswordForm;
use yii\base\Model;
use usni\library\utils\FlashUtil;
use yii\web\UploadedFile;
use usni\library\utils\FileUploadUtil;
use usni\library\modules\users\utils\UsersPermissionUtil;
use yii\caching\DbDependency;
use usni\library\modules\notification\models\Notification;
use usni\library\modules\notification\utils\NotificationUtil;
use usni\library\managers\UploadInstanceManager;
/**
 * Contains utility functions related to Users.
 * 
 * @package usni\library\modules\users\utils
 */
class UserUtil
{
    /**
     * Validate and save User data.
     * @param UserEditForm $model
     * @return boolean
     */
    public static function validateAndSaveUserData($model)
    {
        if(Model::validateMultiple([$model->user, $model->person, $model->address]))
        {
            $config = [
                        'model'             => $model->person,
                        'attribute'         => 'profile_image',
                        'uploadInstanceAttribute' => 'uploadInstance',
                        'type'              => 'image',
                        'savedAttribute'    => 'savedImage',
                        'fileMissingError'  => UsniAdaptor::t('application', 'Please upload image'),
                  ];
            $uploadInstanceManager = new UploadInstanceManager($config);
            $result = $uploadInstanceManager->processUploadInstance();
            if($result === false)
            {
                return false;
            }
            if($model->person->save(false))
            {
                $savedImage                     = $model->person->profile_image;
                $model->person->uploadInstance  = UploadedFile::getInstance($model->person, 'profile_image');
                if($model->person->profile_image != null)
                {
                    $config = [
                                    'model'             => $model->person, 
                                    'attribute'         => 'profile_image', 
                                    'uploadInstance'    => $model->person->uploadInstance, 
                                    'savedFile'         => $savedImage
                              ];
                    FileUploadUtil::save('image', $config);
                }
                $model->user->person_id             = $model->person->id;
                if($model->user->isNewRecord)
                {
                    $model->user->setPasswordHash($model->user->password);
                    $model->user->generateAuthKey();
                }
                $model->user->save(false);
                if($model->address != null)
                {
                    $model->address->relatedmodel       = 'Person';
                    $model->address->relatedmodel_id    = $model->person->id;
                    $model->address->type               = Address::TYPE_DEFAULT;
                    $model->address->save(false);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Create super user for the system.
     * @param array $data
     * @return void
     */
    public static function createSuperUser($data = [])
    {
        $model              = new UserEditForm(['scenario' => 'supercreate']);
        $model->user        = new User(['scenario' => 'supercreate']);
        $model->person      = new Person(['scenario' => 'supercreate']);
        $model->address     = new Address(['scenario' => 'supercreate']);
        
        $email     = ArrayUtil::getValue($data, 'superEmail', 'demo@uicms.org');
        $password  = ArrayUtil::getValue($data, 'superPassword', 'admin');
        $username  = ArrayUtil::getValue($data, 'superUsername', User::SUPER_USERNAME);
        $timezone  = ArrayUtil::getValue($data, 'timezone', TimezoneUtil::getCountryTimezone('IN'));
        $firstname = ArrayUtil::getValue($data, 'firstName', 'Super');
        $lastname  = ArrayUtil::getValue($data, 'lastName', 'Admin');
        
        $userData       = ['username' => $username, 'timezone' => $timezone, 'status' => User::STATUS_ACTIVE, 'password' => $password];
        $personData     = ['email' => $email, 'firstname' => $firstname, 'lastname' => $lastname, 'mobilephone' => ''];
        $addressData    = ['country' => 'IN', 'state' => 'Delhi', 'address1' => '302', 'address2' => '9A/1, W.E.A, Karol Bagh', 'city' => 'New Delhi', 
                           'postal_code' => 110005, 'status' => User::STATUS_ACTIVE];
        $model->user->attributes    = $userData;
        $model->person->attributes  = $personData;
        $model->address->attributes = $addressData;
        $model->user->setPasswordHash($password);
        if(self::validateAndSaveUserData($model))
        {
            return $model->user;
        }
        else
        {
            return false;
        }
    }

    /**
     * Delete user groups.
     * @param Model $model
     * @return void
     */
    public static function deleteGroupsForUser($model)
    {
        $memberType = strtolower(UsniAdaptor::getObjectClassName($model));
        UsniAdaptor::db()->createCommand()
                      ->delete(GroupMember::tableName(),
                               'member_id = :mid AND member_type = :mt',
                               array(':mid' => $model->id, ':mt' => $memberType))->execute();
    }

    /**
     * Saves user groups.
     * @param Model $model
     * @return void
     */
    public static function saveGroups($model)
    {
        $memberType = strtolower(UsniAdaptor::getObjectClassName($model));
        self::deleteGroupsForUser($model);
        if(is_array($model->groups) && !empty($model->groups))
        {
            foreach($model->groups as $group)
            {
                $groupMember              = new GroupMember(['scenario' => 'create']);
                $groupMember->member_type = $memberType;
                $groupMember->member_id   = $model->id;
                $groupMember->group_id    = $group;
                $groupMember->save();
            }
        }
    }

    /**
     * Gets user ip address.
     * @return string
     * @see http://stackoverflow.com/questions/10517371/ip-address-of-the-machine-in-php-gives-1-but-why
     */
    public static function getUserIpAddress()
    {
        $hostAddress = UsniAdaptor::app()->request->getUserIP();
        if($hostAddress == '::1')
        {
            return '127.0.0.1';
        }
        return $hostAddress;
    }

    /**
     * Render top nav menu for loguout.
     * @return string
     */
    public static function renderTopnavMenu()
    {
        $model   = UsniAdaptor::app()->user->getUserModel();
        $content = CacheUtil::get($model->username . '-userTopNavMenu');
        if($content === false)
        {
            $headerLink     = FA::icon('user') . "\n" .
                              UiHtml::tag('span', $model->username) . "\n" .
                              FA::icon('caret-down');
            $headerLink     = UiHtml::a($headerLink, '#', array('data-toggle' => 'dropdown', 'class' => 'dropdown-toggle'));

            $items          = static::getTopNavItems();
            $listItems      = Dropdown::widget(['items'         => $items,
                                                'options'       => ['class' => 'dropdown-menu dropdown-menu-right'],
                                                'encodeLabels'  => false
                                               ]);
            $content = $headerLink . $listItems;
            CacheUtil::set($model->username . '-userTopNavMenu', $content);
            CacheUtil::setModelCache(User::className(), $model->username . '-userTopNavMenu');
        }
        return $content;
    }

    /**
     * Get top navigation items.
     * @return array
     */
    public static function getTopNavItems()
    {
        $model          = UsniAdaptor::app()->user->getUserModel();
        $items          = array();
        $logoutLabel    = FA::icon('sign-out') . "\n" . UsniAdaptor::t('users', 'Logout');
        $item           = ['label'      => $logoutLabel,
                           'url'        => UsniAdaptor::createUrl('/users/default/logout'),
                           'visible'    => true];
        $items[]        = $item;

        //Profile link
        $profileLabel   = FA::icon('user') . "\n" . UsniAdaptor::t('users', 'My Profile');
        $item           = ['label'      => $profileLabel,
                           'url'        => UsniAdaptor::createUrl('/users/default/view', ['id' => $model->id]),
                           'visible'    => true];
        $items[]        = $item;

        if(AuthManager::checkAccess($model, 'user.change-password'))
        {
            $passwordLabel      = FA::icon('lock') . "\n" . UsniAdaptor::t('users', 'Change Password');
            $item               = ['label'      => $passwordLabel,
                                   'url'        => UsniAdaptor::createUrl('/users/default/change-password', ['id' => $model->id]),
                                   'visible'    => true];
            $items[]                = $item;
        }
        return $items;
    }

    /**
     * Gets user browse by dropdown options
     * @param ActiveRecord $model
     * @param string $attribute
     * @return array
     */
    public static function getBrowseByDropDownOptions($model, $attribute, $otherPermission, $loggedInModel)
    {
        $modelClass      = get_class($model);
        $models          = $modelClass::find()->orderBy(['id' => SORT_ASC])->all();
        if(AuthManager::checkAccess($loggedInModel, $otherPermission))
        {
            $data = ArrayUtil::map($models, 'id', $attribute);
        }
        else
        {
            $filteredModels  = array();
            foreach($models as $value)
            {
                //If ids are not equal
                if($value->id != $model->id)
                {
                    //If created by user are same so that logged in user can see users created by him
                    if($value->created_by == $loggedInModel->id)
                    {
                        $filteredModels[] = $value;
                    }
                    //If logged in user is viewing other model.
                    if($value->id == $loggedInModel->id)
                    {
                        $filteredModels[] = $value;
                    }
                }
            }
            $data = ArrayUtil::map($filteredModels, 'id', $attribute);
        }
        //If logged in user is not super user
        if($loggedInModel->id != User::SUPER_USER_ID)
        {
            if(array_key_exists(User::SUPER_USER_ID, $data))
            {
                unset($data[User::SUPER_USER_ID]);
            }
        }
        if(array_key_exists($model->id, $data))
        {
            unset($data[$model->id]);
        }
        return $data;
    }

    /**
     * Sets default auth assignments.
     * @param string $authName
     * @param string $authType
     * @return void
     */
    public static function setDefaultAuthAssignments($authName, $authType)
    {
        $permissions = [$authType . '.update', $authType . '.view', $authType . '.change-password'];
        AuthManager::addAuthAssignments($permissions, $authName, $authType);
    }

    /**
     * Activates user.
     * @param string $hash
     * @param string $email
     * @return boolean
     */
    public static function activateUser($tableName, $hash, $email)
    {
        $hash          = base64_decode($hash);
        $query         = new \yii\db\Query();
        $personTable   = UsniAdaptor::app()->db->tablePrefix . 'person';
        $user          = $query->select('tu.*, tp.email')->from($tableName . ' tu, ' . $personTable . ' tp')
                            ->where('tu.password_hash = :ph AND tu.person_id = tp.id')->params([':ph' => $hash])->one();
        if($user['email'] == $email)
        {
            $data   = ['status' => User::STATUS_ACTIVE, 'modified_by' => User::SUPER_USER_ID, 'modified_datetime' => date('Y-m-d H:i:s')];
            $result = UsniAdaptor::app()->db->createCommand()->update($tableName, $data, 'id = :id', [':id' => $user['id']])->execute();
            if($result)
            {
                return $user;
            }
        }
        return false;
    }
    
    /**
     * Get top nav menu content
     * @param User $user
     * @return type
     */
    public static function getTopnavMenuItem($user)
    {
        if(AuthManager::checkAccess($user, 'user.settings'))
        {
            $usersLabel          = FA::icon('user') . "\n" . UsniAdaptor::t('users', 'User');
            $item                = ['label'      => $usersLabel,
                                    'url'        => UsniAdaptor::createUrl('/users/default/settings'),
                                    'visible'    => true];
            return $item;
        }
        return null;
    }
    
    /**
     * Process change password action.
     * @param int $id
     * @param Array $postData
     * @return ChangePasswordForm|false
     */
    public static function processChangePasswordAction($id, $postData, $loggedInUserModel)
    {
        $user           = User::findOne($id);
        $isPermissible  = UsersPermissionUtil::doesUserHavePermissionToPerformAction($user, $loggedInUserModel, 'user.changepasswordother');
        if($isPermissible)
        {
            $model  = new ChangePasswordForm(['user' => $user]);
            if ($model->load($postData) && $model->validate() && $model->resetPassword())
            {
                $model->sendMail();
                FlashUtil::setMessage('changepassword', UsniAdaptor::t('userflash', 'Password has been changed successfully.'));
                //Set to null
                $model->newPassword     = null;
                $model->confirmPassword = null;
            }
            return $model;
        }
        return false;
    }
    
    /**
     * Get address labels
     * @return array
     */
    public static function getAddressLabels()
    {
        return [
            'address1'          => UsniAdaptor::t('users', 'Address1'),
            'address2'          => UsniAdaptor::t('users', 'Address2'),
            'city'              => UsniAdaptor::t('city', 'City'),
            'state'             => UsniAdaptor::t('state', 'State'),
            'country'           => UsniAdaptor::t('country', 'Country'),
            'postal_code'       => UsniAdaptor::t('users', 'Postal Code'),
            'status'            => UsniAdaptor::t('application', 'Status'),
            'relatedmodel'      => UsniAdaptor::t('users','Related Model'),
            'relatedmodel_id'   => UsniAdaptor::t('users','Related Id'),
            'useBillingAddress' => UsniAdaptor::t('users','Same As Billing Address'),
        ];
    }
    
    /**
     * Get person labels
     * @return array
     */
    public static function getPersonLabels()
    {
        return [
                    'id'                => UsniAdaptor::t('application', 'Id'),
                    'firstname'         => UsniAdaptor::t('users','First Name'),
                    'lastname'          => UsniAdaptor::t('users','Last Name'),
                    'mobilephone'       => UsniAdaptor::t('users','Mobile'),
                    'officephone'       => UsniAdaptor::t('users','Office Phone'),
                    'officefax'         => UsniAdaptor::t('users','Office Fax'),
                    'email'             => UsniAdaptor::t('users','Email'),
                    'fullName'          => UsniAdaptor::t('users','Full Name'),
                    'profile_image'     => UsniAdaptor::t('users','Profile Image')
                ];
    }
    
    /**
     * Get user labels
     * @return array
     */
    public static function getUserLabels()
    {
        return [
            'username'          => UsniAdaptor::t('users', 'Username'),
            'password'          => UsniAdaptor::t('users', 'Password'),
            'confirmPassword'   => UsniAdaptor::t('users', 'Confirm Password'),
            'timezone'          => UsniAdaptor::t('users', 'Timezone'),
            'status'            => UsniAdaptor::t('application', 'Status'),
            'groups'            => UsniAdaptor::t('auth',  'Group')
        ];
    }
    
    /**
     * Get password instructions.
     * @return string
     */
    public static function getPasswordInstructions()
    {
        return UsniAdaptor::t('userflash', '<div class="notifications">
                                                    <ul>
                                                        <li>Must contains one digit from 0-9</li>
                                                        <li>Must contains one alphabet(case insensitive)</li>
                                                        <li>Must contains one special symbol</li>
                                                        <li>Match anything with previous condition checking
                                                                {6,20} length at least 6 characters and maximum of 20</li>
                                                    </ul>
                                                </div>');
    }
    
    /**
     * Gets dropdown field select data.
     * @param string $modelClass
     * @return array
     */
    public static function getDropdownDataBasedOnModel($modelClass)
    {
        $key    = $modelClass . 'DropdownList';
        $data   = CacheUtil::get($key);
        if($data === false)
        {
            $data = ArrayUtil::map($modelClass::find()->indexBy('username')->all(), 'id', 'username');
            CacheUtil::set($key, $data);
            CacheUtil::setModelCache($modelClass, $key);
        }
        return $data;
    }
    
    /**
     * Update model attribute with bulk edit
     * @param string $modelClassName
     * @param string $key
     * @param string $value
     * @param Model $user
     */
    public static function updateModelAttributeWithBulkEdit($modelClassName, $key, $value, $user)
    {
        $userFields         = ['status', 'timezone', 'groups'];
        $addressFields      = ['city', 'country', 'state', 'postal_code'];
        $personFields       = ['firstname', 'lastname'];
        $model              = new $modelClassName();
        $model->scenario    = 'bulkedit';
        if(in_array($key, $userFields))
        {
            if($value !== null && $value !== '')
            {
                if($key == 'groups')
                {
                    if(is_string($value) || is_int($value))
                    {
                        $value = [strval($value)];
                    }
                }
                $user->$key = $value;
                $user->save();
            }
        }
        elseif(in_array($key, $addressFields))
        {
            if(!empty($value))
            {
                $user->address->$key = $value;
                $user->address->save();
            }
        }
        elseif(in_array($key, $personFields))
        {
            if(!empty($value))
            {
                $user->person->$key = $value;
                $user->person->save();
            }
        }
    }
    
    /**
     * Get no profile image
     * @param array $htmlOptions
     * @return string
     */
    public static function getNoProfileImage($htmlOptions = [])
    {
        $noImagePath  = UsniAdaptor::app()->getModule('users')->getBasePath() . DS . 'assets' . DS . 'images' . DS . 'no_profile.jpg';
        $publishedData = UsniAdaptor::app()->assetManager->publish($noImagePath);
        if(empty($htmlOptions))
        {
            $htmlOptions = ['width' => 64, 'height' => 64];
        }
        return UiHtml::img($publishedData[1], $htmlOptions);
    }
    
    /**
     * Get user by id
     * @return array
     */
    public static function getUserById($id)
    {
        $connection             = UsniAdaptor::app()->getDb();
        $tableName              = $connection->tablePrefix . 'user';
        $peTableName            = $connection->tablePrefix . 'person';
        $adTableName            = $connection->tablePrefix . 'address';
        $dependency             = new DbDependency(['sql' => "SELECT MAX(modified_datetime) FROM $tableName WHERE id = :id", 'params' => [':id' => $id]]);
        $sql                    = "SELECT u.*, pe.firstname, pe.lastname, pe.email, pe.mobilephone, pe.profile_image,
                                   ad.address1, ad.address2, ad.city, ad.state, ad.country, ad.postal_code
                                   FROM $tableName u, $peTableName pe, $adTableName ad 
                                   WHERE u.id = :uid AND u.person_id = pe.id AND pe.id = ad.relatedmodel_id AND ad.relatedmodel = :rm AND ad.type = :type";
        return $connection->createCommand($sql, [':uid' => $id, ':rm' => 'Person', ':type' => Address::TYPE_DEFAULT])
                          ->cache(0, $dependency)->queryOne();
    }
    
    /**
     * Generate random password.
     * @return string.
     */
    public static function generateRandomPassword()
    {
        $chars      = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $password   = substr( str_shuffle( $chars ), 0, 8);
        return $password;
    }
    
    /**
     * Generate special character.
     * @return string.
     */
    public static function generateSpecialChar()
    {
        $chars      = "!@#$%^&*";
        $chosenChar   = substr( str_shuffle( $chars ), 0, 1);
        return $chosenChar;
    }
    
    /**
     * Get name for record editor.
     * @param int $value
     * @return string
     */
    public static function getRecordEditorName($value)
    {
        if($value == 0)
        {
            return UsniAdaptor::t('application','(not set)');
        }
        $data = UserUtil::getUserById($value);
        if($data !== false)
        {
            return $data['username'];
        }
        else
        {
            return UsniAdaptor::t('application','(not set)');
        }
    }
    
    /**
     * Send new user notification
     * @param UiEmailNotification $emailNotification
     * @return boolean
     */
    public static function sendNewUserNotification($emailNotification)
    {
        $mailer             = UsniAdaptor::app()->mailer;
        $mailer->emailNotification = $emailNotification;
        $message            = $mailer->compose();
        list($fromName, $fromAddress) = NotificationUtil::getSystemFromAddressData();
        $isSent             = $message->setFrom([$fromAddress => $fromName])
                            ->setTo($emailNotification->person->email)
                            ->setSubject($emailNotification->getSubject())
                            ->send();
        $data               = serialize(array(
                                'fromName'    => $fromName,
                                'fromAddress' => $fromAddress,
                                'toAddress'   => $emailNotification->person->email,
                                'subject'     => $emailNotification->getSubject(),
                                'body'        => $message->toString()));
        $status             = $isSent === true ? Notification::STATUS_SENT : Notification::STATUS_PENDING;
        //Save notification
        return NotificationUtil::saveEmailNotification($emailNotification, $status, $data);
    }
}