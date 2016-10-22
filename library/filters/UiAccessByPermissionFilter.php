<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\filters;

use usni\library\modules\auth\managers\AuthManager;
use usni\library\utils\ArrayUtil;
use Yii;
use yii\web\ForbiddenHttpException;
use usni\UsniAdaptor;

/**
 * Filter that automatically checks if the user has access to the current controller action based
 * on permissions.
 * 
 * @package usni\library\filters
 */
class UiAccessByPermissionFilter extends \yii\base\ActionFilter
{
    /**
     * Action to permission map.
     * @var array
     */
    public $actionToPermissionsMap;

    /**
     * @inheritdoc
     */
	public function beforeAction($action)
	{
        //Allow all group user to grid view settings, preview, grid preview, change language
        if ($action->id == 'grid-view-settings' || $action->id == 'preview' || $action->id == 'grid-preview' || $action->id == 'change-language')
        {
            return true;
        }
        
        $user = UsniAdaptor::app()->user;
        if($user->isGuest)
        {
            $this->processPermissionFilterForNonLoggedInUser($user);
        }
        else
        {
            $permissionsMap   = $this->actionToPermissionsMap;
            $permission       = ArrayUtil::getValue($permissionsMap, $action->id);
            $userModel        = $user->getUserModel();
            $isPermissible    = AuthManager::checkAccess($userModel, $permission);
            if($isPermissible)
            {
                return true;
            }
            else
            {
                throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
            }
        }
	}

    /**
     * Process permission filter for non logged in user.
     * @param yii\web\User $user
     * @return void
     */
    protected function processPermissionFilterForNonLoggedInUser($user)
    {
        $user->loginRequired();
    }
}