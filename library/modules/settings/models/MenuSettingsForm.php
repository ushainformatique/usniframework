<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\models;

use usni\UsniAdaptor;
use usni\library\components\UiFormModel;
/**
 * MenuSettingsForm class file.
 * 
 * @package usni\library\modules\settings\models
 */
class MenuSettingsForm extends UiFormModel
{
    /**
     * @var string 
     */
    public $containerClass;
    /**
     * @var string 
     */
    public $itemClass;
    /**
     * @var string 
     */
    public $sortOrder;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                    ['sortOrder',        'required'],
                    ['itemClass',        'string'],
                    ['containerClass',   'string'],
                    [['sortOrder', 'itemClass', 'containerClass'], 'safe']
               ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
                    'sortOrder'       => UsniAdaptor::t('application', 'Sort Order'),
                    'itemClass'       => UsniAdaptor::t('settings', 'Item Class'),
                    'containerClass'  => UsniAdaptor::t('settings', 'Container Class')
               ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
                    'sortOrder'       => UsniAdaptor::t('settingshint', 'Menu Sort Order'),
                    'itemClass'       => UsniAdaptor::t('settingshint', 'Menu Item Class'),
                    'containerClass'  => UsniAdaptor::t('settingshint', 'Menu Container Class')
               ];
    }
}