<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\models;

use usni\library\components\UiBaseActiveRecord;
use usni\library\utils\FileUploadUtil;
use usni\UsniAdaptor;
use usni\library\validators\UiEmailValidator;
use usni\library\validators\UiFileSizeValidator;
use usni\library\utils\ArrayUtil;
/**
 * This is the model class for table "person. The followings are the available model relations.
 * 
 * @package usni\library\modules\users\models
 */
class Person extends UiBaseActiveRecord
{
    /**
     * Upload File Instance.
     * @var string
     */
    public $savedImage;
    
    /**
     * Upload File Instance.
     * @var string
     */
    public $uploadInstance;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        if($this->checkIfExtendedConfigExists())
        {
            $configInstance = $this->getExtendedConfigClassInstance();
            $labels         = $configInstance->attributeLabels();
        }
        else
        {
            $labels = [
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
        return parent::getTranslatedAttributeLabels($labels);
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        if($this->checkIfExtendedConfigExists())
        {
            $configInstance = $this->getExtendedConfigClassInstance();
            $scenarios      = $configInstance->scenarios();
            return $scenarios;
        }
        else
        {
            $scenarios                  = parent::scenarios();
            $commonAttributes           = ['email', 'firstname', 'lastname', 'mobilephone', 'officephone', 'officefax'];
            $scenarios['create']        = $scenarios['update'] = ArrayUtil::merge($commonAttributes, ['profile_image']);
            $scenarios['registration']  = $scenarios['editprofile'] = ['firstname', 'lastname', 'email',  'mobilephone'];
            $scenarios['supercreate']   = $commonAttributes;
            $scenarios['bulkedit']      = ['firstname', 'lastname', 'mobilephone'];
            $scenarios['deleteimage']   = ['profile_image'];
            return $scenarios;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        if($this->checkIfExtendedConfigExists())
        {
            $configInstance = $this->getExtendedConfigClassInstance();
            $rules          = $configInstance->rules();
            return $rules;
        }
        else
        {
            return array(
                //Person rules
                [['firstname', 'lastname'],         'required'],
                [['firstname', 'lastname'],         'match', 'pattern' => '/^[A-Z._]+$/i'],
                [['firstname', 'lastname'],         'string', 'max' => 32],
                ['email',                           'required'],
                ['email',                           'unique', 'targetClass' => Person::className(), 'on' => ['create', 'registration']],
                ['email',                           'unique', 'targetClass' => Person::className(), 'on' => ['update', 'editprofile'], 
                                                    'filter' => ['!=', 'id', $this->id]],
                ['email',                           UiEmailValidator::className()],
                [['mobilephone', 'officephone', 'officefax'], 'number'],
                [['profile_image'],                 'file'],
                ['profile_image',                   UiFileSizeValidator::className()],
                [['uploadInstance'], 'image', 'skipOnEmpty' => true, 'extensions' => 'jpg, png, gif, jpeg'],
                [['firstname', 'lastname', 'mobilephone', 'officephone', 'officefax'],  'safe'],
            );
        }
    }

    /**
     * Get full name for the user.
     * @return string
     */
    public function getFullName()
    {
        if($this->firstname != null && $this->lastname != null)
        {
            return $this->firstname . ' ' . $this->lastname;
        }
        else
        {
            return UsniAdaptor::t('application', '(not set)');
        }
    }

    /**
     * Gets profile image.
     * @param array $htmlOptions
     * @return mixed
     */
    public function getProfileImage($htmlOptions = array())
    {
        return FileUploadUtil::getThumbnailImage($this, 'profile_image', $htmlOptions);
    }

    /**
     * Get address for the person.
     * @return \Address
     */
    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['relatedmodel_id' => 'id'])
                    ->where('relatedmodel = :rm AND type = :type', [':rm' => 'Person', ':type' => Address::TYPE_DEFAULT]);
    }
    
    /**
     * @inheritdoc
     */
    public static function getLabel($n = 1)
    {
        return UsniAdaptor::t('users', 'Person');
    }
}