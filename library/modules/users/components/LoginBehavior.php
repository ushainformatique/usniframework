<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\components;
use usni\UsniAdaptor;
use yii\base\Behavior;
use usni\library\components\UiWebUser;
use usni\library\modules\users\utils\UserUtil;
use yii\web\UserEvent;
use usni\library\utils\CacheUtil;
/**
 * LoginBehavior class file.
 * The methods would be used when afterLogin event is raised by the application.
 * 
 * @package usni\library\modules\users\components
 */
class LoginBehavior extends Behavior
{
    /**
     * Attach events with this behavior.
     * @return array
     */
    public function events()
    {
        return array(UiWebUser::EVENT_AFTER_LOGIN => array($this, 'processAfterLogin'));
    }

    /**
     * Called after successfully logging into the system.
     * @param
     */
    public function processAfterLogin(UserEvent $event)
    {
        $user       = $event->identity;
        $returnUrl  = null;
        if($event->cookieBased === false)
        {
            $user->last_login = date('Y-m-d H:i:s');
            $user->login_ip   = UserUtil::getUserIpAddress();
            $user->save();
            //Set menu permissions null so that fresh assignemnets are retrieved
            UsniAdaptor::app()->user->setUserPermissions(null);
            //Clear left nav menu so that if a a new permission is assigned to user thus left menu is refreshed
            CacheUtil::delete($user->username . '-allModulesMenuItems');
            $returnUrl        = UsniAdaptor::app()->user->getReturnUrl();
        }
    }
}