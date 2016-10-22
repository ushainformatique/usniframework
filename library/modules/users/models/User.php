<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\models;

use usni\library\components\UiSecuredActiveRecord;
use usni\library\modules\auth\components\IAuthIdentity;
use usni\UsniAdaptor;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\modules\users\utils\UserUtil;
use usni\library\utils\ConfigurationUtil;
use usni\library\utils\ArrayUtil;
/**
 * User is the base class for table tbl_user.
 *
 * It also consist of extra attributes required to store the information in different scenarios,
 * for example $newPassword during change password scenario.
 *
 * @package usni\library\modules\users\models
 */
class User extends UiSecuredActiveRecord implements IAuthIdentity
{
    /**
     * Misc constants.
     */
    const STATUS_PENDING    = 2;
    const SUPER_USERNAME    = 'super';
    const SUPER_USER_ID     = 1;

    /**
     * Notification constants
     */
    const NOTIFY_CREATEUSER        = 'createUser';
    const NOTIFY_CHANGEPASSWORD    = 'changepassword';
    const NOTIFY_FORGOTPASSWORD    = 'forgotpassword';
    
    /**
     * Store password during change password or forgot password.
     * @var string
     */
    public $newPassword;
    /**
     * Store confirm password which matches the password entered by user.
     * @var string
     */
    public $confirmPassword;
    /**
     * Store password during user creation.
     * @var string
     */
    public $password;
    /**
     * Contain groups assigned to user when creating or updating the user
     * @var array
     */
    public $groups = [];

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = ArrayUtil::merge(parent::attributeLabels(), UserUtil::getUserLabels(), [
            'id'                => UsniAdaptor::t('application', 'Id'),
            'newPassword'       => UsniAdaptor::t('users', 'New Password'),
            'last_login'        => UsniAdaptor::t('users', 'Last Login'),
            'login_ip'          => UsniAdaptor::t('users', 'Last Login IP'),
            'person_id'         => UsniAdaptor::t('users', 'Person'),
            'email'             => UsniAdaptor::t('users', 'Email'),
            'groups'            => UsniAdaptor::t('auth', 'Groups'),
        ]);
        return parent::getTranslatedAttributeLabels($labels);
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios                  = parent::scenarios();
        $commonAttributes           = ['username','timezone', 'status', 'groups', 'type'];
        $scenarios['create']        = ArrayUtil::merge($scenarios['create'], $commonAttributes, ['password', 'confirmPassword']);
        $scenarios['update']        = $commonAttributes;
        $scenarios['supercreate']   = ArrayUtil::merge($commonAttributes, ['password']);
        $scenarios['registration']  = ArrayUtil::merge($commonAttributes, ['password', 'confirmPassword']);
        $scenarios['editprofile']   = $commonAttributes;
        $scenarios['bulkedit']      = ['timezone', 'status', 'groups'];
        return $scenarios;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array(
            //User model fields rule.
            [['username'],                      'required'],
            ['username',                        'trim'],
            ['type',                            'required', 'except' => 'supercreate'],
            ['type',                            'default', 'value' => 'system'],
            ['username',                        'unique', 'targetClass' => static::getTargetClassForUniqueUsername(), 'on' => 'create'],
            ['username', 'unique', 'targetClass' => static::getTargetClassForUniqueUsername(), 'filter' => ['!=', 'id', $this->id], 'on' => 'update'],
            ['username',                        'match', 'pattern' => '/^[A-Z0-9._]+$/i'],
            //@see http://www.zorched.net/2009/05/08/password-strength-validation-with-regular-expressions/
            ['password',                        'match', 'pattern' => '/^((?=.*\d)(?=.*[a-zA-Z])(?=.*\W).{6,20})$/i', 'except' => 'supercreate'],
            ['password',                        'required', 'on' => ['create', 'registration']],
            ['timezone',                        'required', 'except' => ['registration', 'default']],
            ['confirmPassword',                 'required', 'on' => ['create', 'registration']],
            ['status',                          'default', 'value' => User::STATUS_PENDING],
            ['groups',                          'safe'],
            [['confirmPassword'], 'compare', 'compareAttribute' => 'password', 'on' => 'create']
        );
    }

    /**
     * Gets status dropdown.
     * @return array
     */
    public static function getStatusDropdown()
    {
        return array(
            User::STATUS_ACTIVE     => UsniAdaptor::t('application','Active'),
            User::STATUS_INACTIVE   => UsniAdaptor::t('application','Inactive'),
            User::STATUS_PENDING    => UsniAdaptor::t('application','Pending')
        );
    }

    /**
     * @inheritdoc
     */
    public static function getLabel($n = 1)
    {
        return ($n == 1) ? UsniAdaptor::t('users', 'User') : UsniAdaptor::t('users', 'Users');
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        UserUtil::saveGroups($this);
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Get scenario to notification key mapping.
     * @return array
     */
    public static function getScenarioToNotificationKeyMapping()
    {
        $mappingData = array('create'            => User::NOTIFY_CREATEUSER,
                             'registration'      => User::NOTIFY_CREATEUSER,
                             'changepassword'    => User::NOTIFY_CHANGEPASSWORD,
                             'forgotpassword'    => User::NOTIFY_FORGOTPASSWORD);
        return $mappingData;
    }

    /**
     * After find populate the groups
     * @return void
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->groups = array_keys(AuthManager::getUserGroups($this->id, get_class($this)));
    }

    /**
     * Get notification key by scenario.
     * @param string $scenario
     * @return mixed
     */
    public static function getNotificationKeyByScenario($scenario)
    {
        $mapping = self::getScenarioToNotificationKeyMapping();
        if(array_key_exists($scenario, $mapping))
        {
            return $mapping[$scenario];
        }
        return null;
    }

    /**
     * Gets name for user.
     * @return string
     */
    public function getName()
    {
        $person = $this->getPerson()->one();
        return $person->getFullName();
    }

    /**
     * Gets auth name.
     * @return string
     */
    public function getAuthName()
    {
        return $this->username;
    }

    /**
     * Gets auth type.
     * @return string
     */
    public function getAuthType()
    {
        return AuthManager::AUTH_IDENTITY_TYPE_USER;
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        $hints  = parent::attributeHints();
        return array_merge(array(
             'username'    => UsniAdaptor::t('userhint', 'Minimum 3 characters. Spaces not allowed. Allowed characters [a-zA-Z0-9._]'),
             'email'       => UsniAdaptor::t('userhint', 'Letters, numbers & periods are allowed with a mail server name. eg test@yahoo.com'),
             'password'    => UsniAdaptor::t('userhint', 'Must be of 6-20 characters. Contains atleast one special, one numeric & one alphabet.'),
             'newPassword' => UsniAdaptor::t('userhint', 'Must be of 6-20 characters. Contains atleast one special, one numeric & one alphabet.'),
             'confirmPassword' => UsniAdaptor::t('userhint', 'Must be of 6-20 characters. Contains atleast one special, one numeric & one alphabet.')
        ), $hints);
    }

    /**
     * Get person for the user.
     * @return ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(Person::className(), ['id' => 'person_id']);
    }

    /**
     * Get address for the user.
     * @return ActiveQuery
     */
    public function getAddress()
    {
        //Read it as select * from address, person where address.relatedmodel_id = person.id  AND person.id = user.person_id
        //Thus when via is used second param in the link correspond to via column in the relation.
        return $this->hasOne(Address::className(), ['relatedmodel_id' => 'id'])
                    ->where('relatedmodel = :rm AND type = :type', [':rm' => 'Person', ':type' => Address::TYPE_DEFAULT])
                    ->via('person');
    }

    /**
     * Get auth key.
     * @return string
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Gets identity
     * @return integer
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Validates auth key.
     * @param string $authKey
     * @return boolean
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return UsniAdaptor::app()->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @return void
     */
    public function setPasswordHash($password)
    {
        $this->password_hash = UsniAdaptor::app()->security->generatePasswordHash($password);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = UsniAdaptor::app()->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = UsniAdaptor::app()->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) 
        {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        $expire = ConfigurationUtil::getValue('users', 'passwordTokenExpiry');
        if($expire === null)
        {
            return true;
        }
        if (empty($token)) 
        {
            return false;
        }
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }
    
    /**
     * Get target class for unique username
     * @return string
     */
    protected static function getTargetClassForUniqueUsername()
    {
        return  static::className();
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if(parent::beforeDelete())
        {
            UserUtil::deleteGroupsForUser($this);
            $person     = $this->person;
            $address    = $this->address;
            $person->delete();
            $address->delete();
            return true;
        }
        else
        {
            return false;
        }
    }
}