<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\models;

use usni\library\components\UiBaseActiveRecord;
use usni\UsniAdaptor;
/**
 * Configuration class file.
 * 
 * @package usni\library\models
 */
class Configuration extends UiBaseActiveRecord
{
	/**
     * @inheritdoc
     */
	public function rules()
	{
		return [
                    [['module', 'key'],                 'required'],
                    [['module', 'key'],                 'string', 'max'=>32],
                    [['id', 'module', 'key', 'value'],  'safe'],
               ];
	}
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenario           = parent::scenarios();
        $scenario['create'] = ['module', 'key', 'value'];
        $scenario['update'] = ['module', 'key', 'value'];
        return $scenario;
    }
    
    /**
     * @inheritdoc
     */
	public function attributeLabels()
	{
		return [
                    'id'        => UsniAdaptor::t('application', 'Id'),
                    'module'    => UsniAdaptor::t('application', 'Module'),
                    'key'       => UsniAdaptor::t('application', 'Key'),
                    'value'     => UsniAdaptor::t('application', 'Value'),
               ];
	}
    
    /**
     * @inheritdoc
     */
    public function getModelConfig()
    {
        return [];
    }
}