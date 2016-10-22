<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\UsniAdaptor;
use usni\library\modules\users\models\User;
use usni\library\modules\auth\managers\AuthManager;

/**
 * UiWebUser class file.
 * 
 * @package usni\library\components
 */
class UiWebUser extends \yii\web\User
{
    /**
     * Behaviors associated to web user.
     * @return array
     */
    public function behaviors()
    {
        return ['login' => 'usni\library\modules\users\components\LoginBehavior'];
    }

    /**
	 * Returns user model.
	 * @return mixed the unique identifier for the user. If null, it means the user is a guest.
	 */
	public function getUserModel()
	{
        if(!$this->getIsGuest())
        {
            return $this->getIdentity();
        }
        return null;
	}

    /**
     * Sets user model.
     * @param User|null $user
     * @return void
     */
    public function setUserModel($user)
    {
        $this->setIdentity($user);
    }

    /**
     * Is logged in user a super user
     * @return boolean
     */
    public function isSuperUser()
    {
        if($this->getUserModel() != null)
        {
            return AuthManager::isSuperUser($this->getUserModel());
        }
        return false;
    }

    /**
	 * Returns user permissions.
	 * @return array
	 */
	public function getUserPermissions()
	{
        $user            = $this->getUserModel();
        $userPermissions = UsniAdaptor::app()->getSession()->get('userPermissions', array());
        if(empty($userPermissions) && $user != null)
        {
            $userPermissions = AuthManager::getUserEffectiveAuthAssignments($user);
            $this->setUserPermissions($userPermissions);
        }
        return $userPermissions;
	}

    /**
     * Sets user permissions.
     * @param array $permissions
     * @return void
     */
    public function setUserPermissions($permissions)
    {
        UsniAdaptor::app()->getSession()->set('userPermissions', $permissions);
    }
}