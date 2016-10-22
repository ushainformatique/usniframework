<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\models;

use usni\library\components\UiSecuredActiveRecord;
use usni\UsniAdaptor;
/**
 * AuthAssignment class file.
 * @package usni\library\modules\auth\models
 */
class AuthAssignment extends UiSecuredActiveRecord
{
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenario               = parent::scenarios();
        $scenario['update']     = $scenario['create'] = ['identity_type', 'identity_name', 'permission', 'resource', 'module'];
        return $scenario;
    }
    
	/**
     * @inheritdoc
     */
	public function rules()
	{
		return [
			[['identity_type', 'identity_name', 'permission'],  'required'],
			['identity_type',                                   'string', 'max' => 16],
            ['identity_name',                                   'string', 'max' => 32],
			['permission',                                      'string', 'max' => 64],
			[['identity_type', 'identity_name', 'permission', 'resource', 'module'],    'safe'],
		];
	}

	/**
     * @inheritdoc
     */
	public function attributeLabels()
	{
		return [
                    'identity_type' => UsniAdaptor::t('notification', 'Identity Type'),
                    'identity_name' => UsniAdaptor::t('notification', 'Identity Name'),
                    'permission'    => UsniAdaptor::t('notification', 'Permission'),
                    'resource'      => UsniAdaptor::t('auth', 'Resource'),
                    'module'        => UsniAdaptor::t('application', 'Module'),
               ];
	}
}
?>