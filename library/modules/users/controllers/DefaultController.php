<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\controllers;

use usni\library\components\UiAdminController;
use usni\library\modules\users\models\LoginForm;
use usni\UsniAdaptor;
use usni\library\modules\users\views\LoginColumnView;
use usni\library\modules\users\models\User;
use usni\library\modules\users\models\UserEditForm;
use usni\library\modules\users\models\Person;
use usni\library\modules\users\models\Address;
use usni\library\modules\users\utils\UserUtil;
use usni\library\modules\users\models\UserSearchForm;
use yii\web\ForbiddenHttpException;
use usni\library\modules\users\views\ChangePasswordView;
use usni\library\utils\ConfigurationUtil;
use usni\library\utils\ArrayUtil;
use usni\library\modules\users\models\SettingsForm;
use usni\library\modules\users\views\SettingsView;
use usni\library\modules\users\views\UserBulkEditView;
use usni\library\modules\users\views\ProfileEditView;
use yii\base\Model;
use usni\library\utils\FlashUtil;
use usni\library\modules\users\utils\UsersPermissionUtil;
/**
 * DefaultController for users.
 * 
 * @package usni\library\modules\users\controllers
 */
class DefaultController extends UiAdminController
{
    /**
     * Redricts to user manage. Index action will be invoked if there is no any action found in url. eg: '/index.php/users'
     * @return void
     */
    public function actionIndex()
    {
        $this->redirect(UsniAdaptor::createUrl('users/default/manage'));
    }

    /**
     * Log in the user in the system.
     * @return void
     */
    public function actionLogin()
    {
        UsniAdaptor::app()->viewHelper->columnView = new LoginColumnView();
        $model      = new LoginForm();
        if (UsniAdaptor::app()->user->isGuest)
        {
            if (isset($_POST['LoginForm']))
            {
                $model->attributes = $_POST['LoginForm'];
                if($model->validate())
                {
                    if($model->login())
                    {
                        $this->goBack();
                    }
                }
            }
        }
        else
        {
            $this->redirect($this->resolveDefaultAfterLoginUrl());
        }
        $viewHelper         = UsniAdaptor::app()->getModule('users')->viewHelper;
        $view               = $viewHelper->getInstance('loginView', ['model' => $model]);
        $content            = $this->renderColumnContent([$view]);
        return $this->render('@usni/themes/bootstrap/views/layouts/login', ['content' => $content]);
    }
    
    /**
     * Logouts the user.
     * @return void
     */
    public function actionLogout()
    {
        UsniAdaptor::app()->user->logout(true);
        return $this->redirect(UsniAdaptor::createUrl('users/default/login'));
    }

    /**
     * @inheritdoc
     */
    public function actionCreate()
    {
        $model              = new UserEditForm(['scenario' => 'create']);
        $model->user        = new User(['scenario' => 'create']);
        $model->person      = new Person(['scenario' => 'create']);
        $model->address     = new Address(['scenario' => 'create']);
        return $this->processUserSave($model);
    }
    
    /**
     * Process user save,
     * @param Model $model
     * @return string
     */
    public function processUserSave($model)
    {
        $scenario           = $model->scenario;
        $postData           = UsniAdaptor::app()->request->post();
        if($model->person->profile_image != null)
        {
            $model->person->savedImage = $model->person->profile_image;
        }
        if ($model->user->load($postData) 
                && $model->person->load($postData) 
                    && $model->address->load($postData))
        {
            if(UserUtil::validateAndSaveUserData($model))
            {
                $model->user->newPassword = $model->user->password;
                if($scenario == 'create')
                {
                    $model->sendMail();
                    FlashUtil::setMessage('userregistration', UsniAdaptor::t('userflash', 'The user is successfully registered with the system.'));
                }
                else
                {
                    UserUtil::setDefaultAuthAssignments($model->user->username, 'user');
                }
                return $this->redirect(UsniAdaptor::createUrl('users/default/manage'));
            }
        }
        $this->setBreadCrumbs($model);
        $editViewClass  = $this->getEditViewClassName();
        $userEditView   = new $editViewClass(['model' => $model]);
        $content        = $this->renderColumnContent([$userEditView]);
        return $this->render($this->getDefaultLayout(), array('content' => $content));
    }
    
    /**
     * Get profile edit view class name.
     * @return string
     */
    protected function getEditViewClassName()
    {
        return ProfileEditView::className();
    }
    
    /**
     * @inheritdoc
     */
    public function actionUpdate($id)
    {
        $model              = new UserEditForm();
        $model->scenario    = 'update';
        $user               = User::findOne($id);
        $model->user        = $user;
        $model->user->scenario      = 'update';
        $model->person      = $user->person;
        $model->person->scenario    = 'update';
        $model->address     = $user->address;
        $model->address->scenario   = 'update';
        return $this->processUserSave($model);
    }

    /**
     * Change password for user.
     * @param integer $id
     * @return void
     */
    public function actionChangePassword($id)
    {
        $model = $this->getChangePasswordModel($id);
        if($model === false)
        {
            throw new ForbiddenHttpException(\Yii::t('yii','You are not authorized to perform this action.'));
        }
        $breadcrumbs      = [
                                [
                                    'label' => UsniAdaptor::t('application', 'Manage') . ' ' . User::getLabel(2),
                                    'url'   => UsniAdaptor::createUrl('users/default/manage')
                                ],
                                [
                                    'label' => UsniAdaptor::t('users','Change Password'),
                                ]
                            ];
        $this->getView()->params['breadcrumbs']  = $breadcrumbs;
        FlashUtil::setMessage('passwordinstructions', UserUtil::getPasswordInstructions());
        $changePasswordViewClassName    = $this->resolveChangePasswordEditViewClassName();
        $changePasswordView             = new $changePasswordViewClassName($model);
        $content                        = $this->renderColumnContent([$changePasswordView]);
        return $this->render($this->getDefaultLayout(), ['content' => $content]);
    }
    
    /**
     * Resolve change ppassword view.
     * @return string
     */
    protected function resolveChangePasswordEditViewClassName()
    {
        return ChangePasswordView::className();
    }
    
    /**
     * Get change password model.
     * @return integer $id.
     * @return ChangePasswordForm|false
     */
    protected function getChangePasswordModel($id)
    {
        return UserUtil::processChangePasswordAction($id, UsniAdaptor::app()->request->post(), UsniAdaptor::app()->user->getUserModel());
    }
        /**
     * @inheritdoc
     */
    public function actionDelete($id)
    {
        $this->processDelete($id);
    }

    /**
     * Process after saving the model.
     * @param array $model User model.
     * @return boolean
     */
    protected function afterModelSave($model)
    {
        $action = $this->getAction()->id;
        if ($action == 'changePassword')
        {
            $model = new User('changepassword');
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function pageTitles()
    {
        $user = UsniAdaptor::t('users', 'User');
        return [
                    'login'             => UsniAdaptor::t('users', 'Login'),
                    'create'            => UsniAdaptor::t('application','Create') . ' ' . $user,
                    'update'            => UsniAdaptor::t('application','Update') . ' ' . $user,
                    'view'              => UsniAdaptor::t('application','View') . ' ' . $user,
                    'manage'            => UsniAdaptor::t('users','Manage Users'),
                    'change-password'   => UsniAdaptor::t('users', 'Change Password'),
                    'forgot-password'   => UsniAdaptor::t('users', 'Forgot Password')
               ];
    }

    /**
     * @inheritdoc
     */
    protected function resolveModelClassName()
    {
        return User::className();
    }

    /**
     * @inheritdoc
     */
    protected function getActionToPermissionsMap()
    {
        $actionToPermissionsMap = parent::getActionToPermissionsMap();
        $additionalPermissions  = [
                                        'change-password' => 'user.change-password',
                                        'change-status'   => 'user.change-status',
                                        'settings'        => 'user.settings'
                                  ];
        return array_merge($additionalPermissions, $actionToPermissionsMap);
    }

    /**
     * @inheritdoc
     */
    protected function resolveModel(& $config = [])
    {
        $scenario       = ArrayUtil::getValue('scenario', $config, 'create');
        $id             = ArrayUtil::getValue('id', $config);
        $modelClassName = ArrayUtil::getValue('modelClassName', $config, $this->resolveModelClassName());
        $model          = parent::resolveModel($config);
        $user           = UsniAdaptor::app()->user->getUserModel();
        if($scenario == 'changepassword')
        {
            $model = $this->loadModel($modelClassName, $id);
            if(UsersPermissionUtil::doesUserHavePermissionToPerformAction($model, $user, 'user.changeotherspassword') === false) //checkUpdateOrDeleteAccessByUser
            {
                throw new ForbiddenHttpException(\Yii::t('yii','You are not authorized to perform this action.'));
            }
        }
        return $model;
    }

    /**
     * @inheritdoc
     */
    protected static function getNonPermissibleActions()
    {
        $nonPermissibleActions = parent::getNonPermissibleActions();
    
        return ArrayUtil::merge($nonPermissibleActions, ['validate-email-address']);
    }

    /**
     * Get search form model class name.
     * @param Model $model
     * @return string
     */
    protected function getSearchFormModelClassName($model)
    {
        return UserSearchForm::className();
    }
    
    /**
     * Change user language in the configuration.
     * @param string $language
     * @return void
     */
    public function actionChangeLanguage($language = null)
    {
        if($language != null)
        {
            $languageManager = UsniAdaptor::app()->languageManager;
            $languageManager->setCookie($language, $languageManager->contentLanguageCookieName);
        }
    }

    /**
     * @inheritdoc
     */
    protected function updateModelAttributeWithBulkEdit($modelClassName, $id, $key, $value)
    {
        $user               = User::findOne($id);
        UserUtil::updateModelAttributeWithBulkEdit($modelClassName, $key, $value, $user);
    }
    
    /**
     * Change status.
     * @param int $id
     * @param int $status
     * @return void
     */
    public function actionChangeStatus($id, $status)
    {
        $user = User::findOne($id);
        $user->status = $status;
        $user->save();
        return $this->renderGridView();
    }
    
    /**
     * Comment settings.
     * @return void
     */
    public function actionSettings()
    {
        $model      = new SettingsForm();
        if(isset($_POST['SettingsForm']))
        {
            $model->attributes = $_POST['SettingsForm'];
            if($model->validate())
            {
                ConfigurationUtil::processInsertOrUpdateConfiguration($model, 'users');
                if(empty($model->errors))
                {
                    FlashUtil::setMessage('userSettingsSaved', UsniAdaptor::t('userflash', 'User settings are saved successfully.'));
                }
            }
        }
        else
        {
            $model->attributes = ConfigurationUtil::getModuleConfiguration('users');
        }
        $breadcrumbs      = [
                                [
                                    'label' => UsniAdaptor::t('users', 'User Settings')
                                ]
                            ];
        $this->getView()->params['breadcrumbs']  = $breadcrumbs;
        $view       = new SettingsView($model);
        $content    = $this->renderColumnContent(array($view));
        return $this->render($this->getDefaultLayout(), array('content' => $content));
    }
    
    /**
     * @inheritdoc
     */
    protected function resolveBulkEditViewClassName()
    {
        return UserBulkEditView::className();
    }
    
    /**
     * @inheritdoc
     */
    protected function getImageFieldName()
    {
        return 'profile_image';
    }
    
    /**
     * Validate email address.
     * @param $hash string
     * @param $email string
     * @return void
     */
    public function actionValidateEmailAddress($hash, $email)
    {
        $tableName  = UsniAdaptor::tablePrefix() . 'user';
        if (UsniAdaptor::app()->user->getIsGuest())
        {
            $user = UserUtil::activateUser($tableName, $hash, $email); 
            if ($user !== false)
            {
                UserUtil::setDefaultAuthAssignments($user['username'], 'user');
                $message = UsniAdaptor::t('users', 'Your email has been validated, Please login to continue.');
            }
            else
            {
                $message = UsniAdaptor::t('users', 'Your email validation fails. Please contact system admin.');
            }
            FlashUtil::setMessage('validateEmail', $message);
            return $this->redirect(UsniAdaptor::createUrl($this->getValidateRedirectUrl()));
        }
        return $this->redirect(\yii\helpers\Url::home());
    }
    
    /**
     * Get validate redirect url
     * @return string
     */
    protected function getValidateRedirectUrl()
    {
        return 'users/default/login';
    }
}