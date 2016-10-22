<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\models;

use yii\base\Model;
use usni\library\modules\users\models\Address;
use usni\library\utils\ArrayUtil;
use usni\library\modules\users\utils\UserUtil;
/**
 * UserBulkEditForm class file
 * 
 * @package usni\library\modules\users\models
 */
class UserBulkEditForm extends Model
{
    //User fields
    public $timezone;
    public $status;
    
    //Address fields
    public $city;
    public $country;
    public $postal_code;
    public $state;
    public $groups = [];

    /**
     * User model
     * @var User 
     */
    public $user;
    
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
        if($this->address == null)
        {
            $this->address = new Address(['scenario' => $this->scenario]);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $commonAttributes       = ['status', 'city', 'country', 'postal_code', 'state', 'groups', 'timezone'];
        $scenarios['bulkedit']  = $commonAttributes;
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                    [['status', 'email', 'city', 'country', 'postal_code', 'state', 'groups', 'timezone'], 'safe'],
               ];
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
        return ArrayUtil::merge(UserUtil::getUserLabels(), UserUtil::getAddressLabels());
    }
    
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        $user       = new User();
        $address    = new Address();
        return ArrayUtil::merge($user->attributeHints(), $address->attributeHints());
    }
}