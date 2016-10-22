<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\models;

use usni\library\components\UiFormModel;
use usni\UsniAdaptor;
/**
 * DatabaseSettingsForm class file.
 * 
 * @package usni\library\modules\settings\models
 */
class DatabaseSettingsForm extends UiFormModel
{
    /**
     * Is schema caching enabled.
     * @var bool
     */
    public $enableSchemaCache;
    /**
     * Schema caching duration
     * @var string
     */
    public $schemaCachingDuration;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                    [['schemaCachingDuration'],   'number', 'integerOnly' => true],
                    ['enableSchemaCache',         'default', 'value' => 1],
                    ['schemaCachingDuration',     'default', 'value' => 3600],
               ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
                    'enableSchemaCache'      => UsniAdaptor::t('settings', 'Enable schema cache'),
                    'schemaCachingDuration'  => UsniAdaptor::t('settings', 'Schema caching duration'),
               ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
                    'enableSchemaCache'       => UsniAdaptor::t('settingshint', 'Enable database schema caching'),
                    'schemaCachingDuration'   => UsniAdaptor::t('settingshint', 'Number of seconds that table metadata can remain valid in cache'),
               ];
    }
}