<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\models;

use yii\base\Model;
use usni\library\modules\users\models\User;
use usni\library\modules\users\models\Address;
use usni\library\modules\users\models\Person;
use usni\library\modules\users\notifications\NewUserEmailNotification;
use usni\library\modules\users\utils\UserUtil;
/**
 * UserEditForm class file
 *
 * @package usni\library\modules\users\models
 */
class UserEditForm extends Model
{
    /**
     * User model
     * @var User 
     */
    public $user;
    
    /**
     * Person model
     * @var Person 
     */
    public $person;
    
    /**
     * Address model
     * @var Address 
     */
    public $address;
    
    /**
     * @inheritdoc
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        if($this->user == null)
        {
            $this->user = new User(['scenario' => $this->scenario]);
        }
        if($this->person == null)
        {
            $this->person = new Person(['scenario' => $this->scenario]);
        }
        if($this->address == null)
        {
            $this->address = new Address(['scenario' => $this->scenario]);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getLabel($n = 1)
    {
        return User::getLabel($n);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [];
    }
    
    /**
     * Sends user registration email
     * @return boolean
     */
    public function sendMail()
    {
        return UserUtil::sendNewUserNotification($this->getEmailNotification());
    }
    
    /**
     * Get email notification
     * @return NewUserEmailNotification
     */
    protected function getEmailNotification()
    {
        return new NewUserEmailNotification(['user' => $this->user, 'person' => $this->person]);
    }
}
