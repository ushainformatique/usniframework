<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\controllers;

use usni\library\components\UiAdminController;
use usni\library\modules\auth\models\Group;
use usni\UsniAdaptor;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\modules\auth\models\AuthAssignmentForm;
use usni\library\modules\auth\views\AuthAssignmentsEditView;
use usni\library\modules\auth\views\AuthPermissionsSelectView;
use usni\library\utils\FlashUtil;
/**
 * PermissionController class file.
 * 
 * @package usni\library\modules\auth\components
 */
class PermissionController extends UiAdminController
{
    /**
     * Manages permissions for group.
     * @param int $id
     * @param string $identityType
     * @return void
     */
    public function actionGroup($id = null)
    {
        $this->getView()->params['breadcrumbs'] =   [
                                                        [
                                                            'label' => Group::getLabel(2),
                                                            'url'   => array('/auth/group/manage')
                                                        ],
                                                        [
                                                            'label' => UsniAdaptor::t('auth', 'Manage Permissions')
                                                        ]
                                                    ];
        return $this->processPermissions($id, AuthManager::AUTH_IDENTITY_TYPE_GROUP);
    }

    /**
     * Process permissions.
     * @param int $authIdentityId
     * @param string $authType
     */
    protected function processPermissions($authIdentityId = null, $authType = AuthManager::AUTH_IDENTITY_TYPE_GROUP)
    {
        $assignmentForm = new AuthAssignmentForm($authIdentityId, $authType);
        if(isset($_POST['AuthAssignmentForm']['authIdentityId']))
        {
            $identityId     = $_POST['AuthAssignmentForm']['authIdentityId'];
            $authIdentity   = AuthManager::getAuthIdentity($identityId, $authType);
            if(isset($_POST['AuthAssignmentForm']['authAssignments']))
            {
                $permissions = $_POST['AuthAssignmentForm']['authAssignments'];
                if(!empty($permissions))
                {
                    AuthManager::addAuthAssignments($permissions, $authIdentity->getAuthName(), $authType);
                    UsniAdaptor::app()->user->setUserPermissions(null);
                }
            }
            else
            {
                AuthManager::deleteAuthAssignments(null, $authIdentity->getAuthName(), $authType);
                UsniAdaptor::app()->user->setUserPermissions(null);
            }
            echo '';
            UsniAdaptor::app()->end();
        }
        FlashUtil::setMessage('savepermissions', UsniAdaptor::t('authflash', 'The permissions are saved successfully'));
        $authAssignmentEditView = new AuthAssignmentsEditView($assignmentForm);
        $content                = $this->renderColumnContent([$authAssignmentEditView]);
        return $this->render($this->getDefaultLayout(), ['content' => $content]);
    }

    /**
     * Get permissions list.
     * @param int $authIdentityId
     * @return void
     */
    public function actionList($authIdentityId)
    {
        $authPermissionForm = new AuthAssignmentForm($authIdentityId, AuthManager::AUTH_IDENTITY_TYPE_GROUP);
        $selectionView      = new AuthPermissionsSelectView($authPermissionForm);
        echo $selectionView->render();
    }

    /**
     * @return null
     */
    protected function resolveModelClassName()
    {
        return null;
    }

    /**
     * Get action to permission map.
     * @return array
     */
    protected function getActionToPermissionsMap()
    {
        return [
                  'group' => 'auth.managepermissions',
                  'list'  => 'auth.managepermissions'
               ];
    }
    
    /**
     * Get page titles.
     * @return array
     */
    public function pageTitles()
    {
        return [
                    'group'       => UsniAdaptor::t('application', 'Manage Permissions')
               ];
    }
}
?>