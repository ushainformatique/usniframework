<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\models;

use usni\library\components\UiFormModel;
use usni\UsniAdaptor;
/**
 * AdminMenuSettingsForm class file.
 * 
 * @package usni\library\modules\settings\models
 */
class AdminMenuSettingsForm extends UiFormModel
{
    public $sortOrder;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                    ['sortOrder',  'required']
               ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
                    'sortOrder'       => UsniAdaptor::t('application', 'Sort Order'),
               ];
    }

    /**
     * Gets attribute hints.
     * @return array
     */
    public function attributeHints()
    {
        return [
                    'sortOrder'     => UsniAdaptor::t('settingshint', 'Menu Sort Order'),
               ];
    }
}