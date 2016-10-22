<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification;

use usni\library\components\UiSecuredModule;
use usni\UsniAdaptor;
use usni\library\modules\notification\utils\NotificationPermissionUtil;
use usni\library\utils\ConfigurationUtil;
/**
 * Loads notification module.
 * @package usni\library\modules\notification
 */
class Module extends UiSecuredModule
{
    /**
     * Overrides to register translations.
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    /**
     * Registers translations.
     */
    public function registerTranslations()
    {
        UsniAdaptor::app()->i18n->translations['notification*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@approot/messages'
        ];
        UsniAdaptor::app()->i18n->translations['notificationflash*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@approot/messages'
        ];
        UsniAdaptor::app()->i18n->translations['notificationhint*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@approot/messages'
        ];
    }
    
    /**
     * Gets permission util.
     * @return string
     */
    public static function getPermissionUtil()
    {
        return NotificationPermissionUtil::className();
    }
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if(parent::beforeAction($action))
        {
            if(ConfigurationUtil::isModuleEnabled($this->id))
            {
                return true;
            }
        }
        return false;
    }
}