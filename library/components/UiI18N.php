<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

/**
 * UiI18N class file.
 * @package usni.library.components
 */
class UiI18N extends \yii\i18n\I18N
{
    public function init()
    {
        parent::init();
        if (!isset($this->translations['applicationhint'])) 
        {
            $this->translations['applicationhint'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => '@approot/messages',
            ];
        }
        if (!isset($this->translations['applicationflash'])) 
        {
            $this->translations['applicationflash'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => '@approot/messages',
            ];
        }
        if (!isset($this->translations['application'])) 
        {
            $this->translations['application'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => '@approot/messages',
            ];
        }
    }
}
?>