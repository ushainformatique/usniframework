<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\models;

use usni\library\components\UiFormModel;
use usni\UsniAdaptor;
/**
 * SettingsForm class file.
 * 
 * @package usni\library\modules\users\models
 */
class SettingsForm extends UiFormModel
{
    /**
     * Duration for password token expiry.
     * @var int
     */
    public $passwordTokenExpiry;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                    [['passwordTokenExpiry'],   'number', 'integerOnly' => true],
                    [['passwordTokenExpiry'],   'required'],
               ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
                    'passwordTokenExpiry' => UsniAdaptor::t('users', 'Password token expiry'),
               ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
                    'passwordTokenExpiry' => UsniAdaptor::t('userhint', 'Duration after which password token expire in seconds'),
               ];
    }
}