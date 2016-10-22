<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth;

use usni\library\components\UiSecuredModule;
use usni\UsniAdaptor;
use usni\library\modules\auth\utils\AuthPermissionUtil;
/**
 * Loads the auth module in the system.
 * @package usni\library\modules\auth
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
        UsniAdaptor::app()->i18n->translations['auth*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@approot/messages',
        ];
        UsniAdaptor::app()->i18n->translations['authhint*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@approot/messages',
        ];
        UsniAdaptor::app()->i18n->translations['authflash*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@approot/messages',
        ];
    }
    
    /**
     * Gets permission util.
     * @return string
     */
    public static function getPermissionUtil()
    {
        return AuthPermissionUtil::className();
    }
}
?>