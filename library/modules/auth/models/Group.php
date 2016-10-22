<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\models;

use usni\library\components\TranslatableActiveRecord;
use usni\library\modules\auth\components\IAuthIdentity;
use usni\UsniAdaptor;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\modules\users\utils\UserUtil;

/**
 * Group active record.
 * 
 * @package usni\library\modules\auth\models
 */
class Group extends TranslatableActiveRecord implements IAuthIdentity
{
    use \usni\library\traits\TreeModelTrait;
    
    /**
     * Group constants.
     */
    const ADMINISTRATORS    = 'Administrators';

    /**
     * Members of the group.
     * @var array
     */
    public $members = [];

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert))
        {
            $this->level = $this->getLevel();
            return true;
        }
       return false;
    }

	/**
     * @inheritdoc
     */
	public function rules()
	{
		return [
                    ['name',       'required'],
                    ['name',       'unique', 'targetClass' => GroupTranslated::className(), 'targetAttribute' => ['name', 'language'], 'on' => 'create'],
                    ['name', 'unique', 'targetClass' => GroupTranslated::className(), 'targetAttribute' => ['name', 'language'], 'filter' => ['!=', 'owner_id', $this->id], 'on' => 'update'],
                    ['status',     'default', 'value' => self::STATUS_ACTIVE],
                    ['parent_id',  'default', 'value' => 0],
                    ['parent_id',  'safe'],
                    [['parent_id', 'name', 'status', 'level', 'members'], 'safe'],
             ];
	}

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenario               = parent::scenarios();
        $scenario['update']     = $scenario['create'] = ['parent_id', 'name', 'status', 'level', 'members'];
        $scenario['bulkedit']   = ['status'];
        return $scenario;
    }
    
    /**
     * @inheritdoc
     */
	public function attributeLabels()
	{
		$labels = [
                        'parent_id'         => UsniAdaptor::t('application', 'Parent'),
                        'name'              => UsniAdaptor::t('application', 'Name'),
                        'description'       => UsniAdaptor::t('application', 'Description'),
                        'status'            => UsniAdaptor::t('application', 'Status'),
                        'members'           => UsniAdaptor::t('auth', 'Members')
                  ];
        return parent::getTranslatedAttributeLabels($labels);
	}

	/**
     * @inheritdoc
     */
    public static function getLabel($n = 1)
    {
        return ($n == 1) ? UsniAdaptor::t('auth', 'Group') : UsniAdaptor::t('auth', 'Groups');
    }

    /**
     * Gets auth name.
     * @return string
     */
    public function getAuthName()
    {
        return $this->name;
    }

    /**
     * Gets auth type.
     * @return string
     */
    public function getAuthType()
    {
        return AuthManager::AUTH_IDENTITY_TYPE_GROUP;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if(empty($this->members))
        {
            $this->members = [];
        }
        AuthManager::addGroupMembers($this, $this->members);
        $this->updateChildrensLevel();
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        $members        = array();
        $membersRecords = AuthManager::getGroupMembers($this);
        foreach($membersRecords as $member)
        {
            $members[] = $member['member_type'] . '-' . $member['member_id'];
        }
        $this->members = $members;
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if(parent::beforeDelete())
        {
            AuthManager::deleteAuthAssignments(null, $this->name, 'group');
            AuthManager::deleteGroupMembers($this);
            $this->deleteModelCache('UserCache');
            $this->setParentAsNullForChildrenOnDelete($this->tableName());
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [ 
                    'name'     => UsniAdaptor::t('applicationhint', 'Minimum 3 characters'),
                    'members'  => UsniAdaptor::t('authhint', 'Members of the group'),
                    'parent_id'=> UsniAdaptor::t('authhint', 'Parent id for the group'),
                    'status'   => UsniAdaptor::t('authhint', 'Status for the group')
               ];
    }

    /**
     * Get members name.
     * return string
     */
    public function getGroupMembers()
    {
        $membersId     = [];
        $membersName   = [];
        $groupMembers  = AuthManager::getGroupMembers($this);
        foreach($groupMembers as $groupMember)
        {
            $membersId[] = $groupMember['member_id'];
        }

        foreach($membersId as $id)
        {
            $member         = UserUtil::getUserById($id);
            $membersName[]  = $member['username'];
        }
        return implode(', ', $membersName);
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }
    
    /**
     * @inheritdoc
     */
    public static function getTranslatableAttributes()
    {
        return ['name'];
    }
    
    /**
     * Get admin group title.
     * @return string
     */
    public static function getAdminGroupTitle()
    {
        return UsniAdaptor::t('auth', 'Administrators');
    }
    
    /**
     * Gets status dropdown.
     * @return array
     */
    public static function getStatusDropdown()
    {
        return [
                    self::STATUS_ACTIVE     => UsniAdaptor::t('application','Active'),
                    self::STATUS_INACTIVE   => UsniAdaptor::t('application','Inactive'),
               ];
    }
}