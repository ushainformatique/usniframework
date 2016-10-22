<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\install;

use usni\library\components\UiWebModule;
use usni\UsniAdaptor;
use yii\helpers\Url;
/**
 * Invoked on the install of the module by the system.
 * @package usni\library\modules\install
 */
class Module extends UiWebModule
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
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) 
        {
            return false;
        }
        /*
         * If app is already installed redirect to home page
         */
        if(UsniAdaptor::app()->isInstalled())
        {
            $url = Url::home();
            UsniAdaptor::app()->getResponse()->redirect($url)->send();
            return;
        }
        return true;
    }

    /**
     * Registers translations.
     */
    public function registerTranslations()
    {   
        UsniAdaptor::app()->i18n->translations['installflash*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@approot/messages'
        ];
        UsniAdaptor::app()->i18n->translations['install*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@approot/messages'
        ];
        UsniAdaptor::app()->i18n->translations['installhint*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@approot/messages'
        ];
    }
}
