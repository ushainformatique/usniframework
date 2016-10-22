<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users;

use usni\library\components\UiSecuredModule;
use usni\library\components\UiHtml;
use usni\library\modules\users\models\User;
use usni\library\modules\users\views\LatestUsersGridView;
use usni\UsniAdaptor;
use usni\library\modules\users\utils\UsersPermissionUtil;

/**
 * Loads the users module in the system.
 * 
 * @package usni\library\modules\users
 */
class Module extends UiSecuredModule
{
    public $controllerNamespace = 'usni\library\modules\users\controllers';

    /**
     * Overrides to register translations.
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    /**
     * Gets dashboard content.
     * @return string
     */
    public function getDashboardContent()
    {
        $user       = new User();
        $user->scenario = 'search';
        $view       = new LatestUsersGridView(['model' => $user]);
        $content    = UiHtml::panelContent($view->render(), array('class' => 'panel-dashboard'));
        return UiHtml::tag('div', $content, ['class' => 'col-sm-6 col-xs-12']);
    }

    /**
     * Registers translations.
     */
    public function registerTranslations()
    {
        UsniAdaptor::app()->i18n->translations['users*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@approot/messages'
        ];
        UsniAdaptor::app()->i18n->translations['userflash*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@approot/messages'
        ];
        UsniAdaptor::app()->i18n->translations['userhint*'] = [
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
        return UsersPermissionUtil::className();
    }
}