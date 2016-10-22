<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\models;

use usni\library\components\UiSecuredActiveRecord;
use usni\UsniAdaptor;
/**
 * Role active record.
 * 
 * @package usni\library\modules\auth\models
 */
class Role extends UiSecuredActiveRecord
{
    use \usni\library\traits\TreeModelTrait;
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
                    ['name',       'unique', 'targetClass' => Role::className(), 'on' => 'create'],
                    ['name', 'unique', 'targetClass' => Role::className(), 'filter' => ['!=', 'id', $this->id], 'on' => 'update'],
                    ['status',     'default', 'value' => self::STATUS_ACTIVE],
                    ['parent_id',  'default', 'value' => 0],
                    ['parent_id',  'safe'],
                    [['parent_id', 'name', 'status', 'level'], 'safe'],
             ];
	}

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenario               = parent::scenarios();
        $scenario['create']     = ['name', 'status', 'parent_id', 'level'];
        $scenario['update']     = ['name', 'status', 'parent_id', 'level'];
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
                  ];
        return parent::getTranslatedAttributeLabels($labels);
	}

    /**
     * @inheritdoc
     */
    public static function getLabel($n = 1)
    {
        return ($n == 1) ? UsniAdaptor::t('auth', 'Role') : UsniAdaptor::t('auth', 'Roles');
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
                    'name'           => UsniAdaptor::t('applicationhint', 'Minimum 3 characters'),
                    'alias'          => UsniAdaptor::t('applicationhint', 'Spaces not allowed. Allowed characters [a-zA-Z0-9_-]'),
                    'description'    => UsniAdaptor::t('applicationhint', 'Description')
               ];
    }
}