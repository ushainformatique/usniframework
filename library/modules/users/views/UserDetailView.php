<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\UsniAdaptor;
use usni\library\views\UiDetailView;
use usni\fontawesome\FA;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\utils\DateTimeUtil;
use usni\library\modules\users\views\UserProfileView;
use usni\library\modules\users\views\UserAddressView;
use usni\library\views\UiTabbedView;
use usni\library\utils\StatusUtil;

/**
 * Detail view for user.
 * @package usni\library\modules\users\views
 */
class UserDetailView extends UiDetailView
{
    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        return [
                  'username',
                  'login_ip',
                  ['attribute' => 'last_login', 'value' => DateTimeUtil::getFormattedDateTime($this->model->last_login)],
                  ['attribute' => 'status',     'value' => StatusUtil::renderLabel($this->model), 'format' => 'html'],
                  'timezone',
                  ['attribute'   =>  'groups', 'value'  => $this->getGroups()],
                  'type'
               ];
    }

    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $content     = null;
        $infoContent = parent::renderContent();
        $person      = $this->model->person;
        $address     = $this->model->address;

        $profileViewClass       = UserProfileView::className();
        $profileViewInstance    = new $profileViewClass($this->getDetailViewConfiguration($person));
        $profileView            = $profileViewInstance->render();
        if($address != null)
        {
            $addressViewClass       = UserAddressView::className();
            $addressViewInstance    = new $addressViewClass($this->getDetailViewConfiguration($address));
            $addressView            = $addressViewInstance->render();
        }
        else
        {
            $addressView    = null;
        }

        $tabs        = ['loginInfo'    => ['label'   => UsniAdaptor::t('application', 'General'),
                                           'content' => $infoContent,
                                           'active'  => true],
                        'profileInfo'  => ['label'   => UsniAdaptor::t('users', 'Profile'),
                                           'content' => $profileView],
                        'addressInfo'  => ['label'   => UsniAdaptor::t('application', 'Address'),
                                           'content' => $addressView]];
        $tabbedView  = new UiTabbedView($tabs);
        $content    .= $tabbedView->render();
        return $content;
    }

    /**
     * @inheritdoc
     */
    protected function getTitle()
    {
        return $this->model->username;
    }

    /**
     * @inheritdoc
     */
    protected function resolveDefaultBrowseByAttribute()
    {
        return 'username';
    }

    /**
     * Get option items.
     * @return array
     */
    protected function getOptionItems()
    {
        $user                   = UsniAdaptor::app()->user->getUserModel();
        $editLink               = null;
        $changePasswordLink     = null;
        $modelPermissionName    = strtolower(UsniAdaptor::getObjectClassName($this->model));
        $passwordLabel          = FA::icon('lock') . "\n" . UsniAdaptor::t('users', 'Change Password');
        $editLabel              = FA::icon('pencil') . "\n" . UsniAdaptor::t('application','Edit');
        if($user->id != $this->model->id && $user->id != $this->model->created_by)
        {
            if(AuthManager::checkAccess($user, $modelPermissionName . '.updateother'))
            {
                $editLink   = $this->getEditUrl();
            }
            if(AuthManager::checkAccess($user, $modelPermissionName . '.changepasswordother'))
            {
                $changePasswordLink = UsniAdaptor::createUrl($this->resolveChangePasswordLink(), ['id' => $this->model->id]);
            }
        }
        else
        {
            if(AuthManager::checkAccess($user, $modelPermissionName . '.update'))
            {
                $editLink   = $this->getEditUrl();
            }
            if(AuthManager::checkAccess($user, $modelPermissionName . '.changepassword'))
            {
                $changePasswordLink = UsniAdaptor::createUrl($this->resolveChangePasswordLink(), ['id' => $this->model->id]);
            }
        }
        $linkArray = [];
        if($editLink != null)
        {
            $linkArray[] = ['label' => $editLabel, 'url' => $editLink];
        }
        if($changePasswordLink != null)
        {
            $linkArray[] = ['label' => $passwordLabel, 'url' => $changePasswordLink];
        }
        return $linkArray;
    }
    
    /**
     * Resolve change password link
     * @return string
     */
    protected function resolveChangePasswordLink()
    {
        return 'users/default/change-password';
    }

    /**
     * @inheritdoc
     */
    protected function renderDetailModelBrowseView()
    {
        $view          = new UserDetailBrowseModelView(['model' => $this->model, 'attribute' => $this->resolveDefaultBrowseByAttribute()]);
        return $view->render();
    }

    /**
     * Get configuration for rendering grid view.
     * @param Model $model
     * @return array
     */
    protected function getDetailViewConfiguration($model)
    {
        return ['model'       => $model];
    }
    
    /**
     * Get groups.
     * @return string
     */
    protected function getGroups()
    {
        $groupNames = AuthManager::getUserGroupNames($this->model);
        if(!empty($groupNames))
        {
            return implode(', ', $groupNames);
        }
        return UsniAdaptor::t('application', '(not set)');
    }
}
?>