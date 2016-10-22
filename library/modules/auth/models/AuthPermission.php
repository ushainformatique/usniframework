<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\models;

use usni\library\components\UiSecuredActiveRecord;
use usni\UsniAdaptor;
/**
 * AuthPermission class file.
 * 
 * @package usni\library\modules\auth\models
 */
class AuthPermission extends UiSecuredActiveRecord
{
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenario               = parent::scenarios();
        $scenario['update']     = $scenario['create'] = ['name', 'resource', 'module', 'alias'];
        return $scenario;
    }
    
	/**
     * @inheritdoc
     */
	public function rules()
	{
		return [
			[['name', 'resource', 'module', 'alias'],     'required'],
			['name',                       'string', 'max' => 64],
            ['resource',                   'string', 'max' => 32],
            ['alias',                      'string', 'max' => 64],
            ['module',                     'string', 'max' => 32],
            ['name',                       'unique', 'targetAttribute' => ['name', 'resource', 'module', 'alias']],
			[['id', 'name', 'resource', 'module', 'alias'], 'safe'],
		];
	}

	/**
     * @inheritdoc
     */
	public function attributeLabels()
	{
		$labels =  [
                        'id'        => UsniAdaptor::t('application', 'Id'),
                        'name'      => UsniAdaptor::t('application', 'Name'),
                        'resource'  => UsniAdaptor::t('auth', 'Resource'),
                        'module'    => UsniAdaptor::t('application', 'Module'),
                        'alias'     => UsniAdaptor::t('application', 'Alias'),
                   ];
        return parent::getTranslatedAttributeLabels($labels);
	}

	/**
     * @inheritdoc
     */
    public static function getLabel($n = 1)
    {
        return ($n == 1) ? UsniAdaptor::t('auth', 'Auth Permission') : UsniAdaptor::t('auth', 'Auth Permissions');
    }
}