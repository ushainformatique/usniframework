<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\controllers;

use usni\library\components\UiAdminController;
use usni\library\modules\settings\models\SiteSettingsForm;
use usni\library\utils\ConfigurationUtil;
use usni\UsniAdaptor;
use yii\web\UploadedFile;
use usni\library\utils\FileUploadUtil;
use usni\library\modules\settings\models\EmailSettingsForm;
use usni\library\utils\ArrayUtil;
use usni\library\modules\settings\utils\SettingsUtil;
use usni\library\modules\settings\models\DatabaseSettingsForm;
use usni\library\modules\settings\views\DatabaseSettingsView;
use usni\library\modules\settings\models\MenuSettingsForm;
use yii\helpers\Json;
use usni\library\modules\settings\views\ModuleSettingsView;
use yii\data\ArrayDataProvider;
use usni\library\utils\CacheUtil;
use usni\library\utils\FlashUtil;
use usni\library\managers\UploadInstanceManager;
/**
 * DefaultController class file
 * 
 * @package usni\library\modules\settings\controllers
 */
class DefaultController extends UiAdminController
{
    /**
     * Runs the installation.
     * @return void
     */
    public function actionIndex()
    {
        $this->redirect(createUrl('settings/default/site'));
    }

    /**
     * Email settings.
     * @return void
     */
    public function actionEmail()
    {
        $model    = new EmailSettingsForm();
        $sendMail = false;
        $isValidData = true;
        if(isset($_POST['EmailSettingsForm']))
        {
            $testEmailAddress = null;
            $sendTestEmail    = null;
            $model->attributes = $_POST['EmailSettingsForm'];
            if($model->validate())
            {
                $attributes = $model->getAttributes();
                $testEmailAddress = $attributes['testEmailAddress'];
                $sendTestEmail    = $attributes['sendTestMail'];
                unset($attributes['testEmailAddress']);
                unset($attributes['sendTestMail']);
                ConfigurationUtil::insertOrUpdateConfiguration('settings', 'emailSettings', serialize($attributes));
                FlashUtil::setMessage('emailSettingsSaved', UsniAdaptor::t('settingsflash', 'Email settings are saved successfully.'));
                if((bool)$model->sendTestMail === true)
                {
                    if($model->testEmailAddress != null)
                    {
                        if($model->sendingMethod == 'smtp')
                        {
                            $isValidSmtpInfo = SettingsUtil::isValidSmtpInfo($model);
                            if($isValidSmtpInfo)
                            {
                                $model->sendTestMail();
                                FlashUtil::setMessage('testEmailSent', UsniAdaptor::t('settingsflash', 'Test email is sent successfully.'));
                            }
                            else
                            {
                                FlashUtil::setMessage('smtpConfNotCorrect', UsniAdaptor::t('settingsflash', 'SMTP configuration is incorrectly provided.'));
                            }
                        }
                        else
                        {
                            $model->sendTestMail();
                            FlashUtil::setMessage('testEmailSent', UsniAdaptor::t('settingsflash', 'Test email is sent successfully.'));
                        }
                    }
                    else
                    {
                        FlashUtil::setMessage('testEmailNotProvided', UsniAdaptor::t('settingsflash', 'Test email address is not provided.'));
                    }
                }
                else
                {
                    if($model->sendingMethod == 'smtp')
                    {
                        $isValidSmtpInfo = SettingsUtil::isValidSmtpInfo($model);
                        if(!$isValidSmtpInfo)
                        {
                            FlashUtil::setMessage('smtpConfNotCorrect', UsniAdaptor::t('settingsflash', 'SMTP configuration is incorrectly provided.'));
                        }
                    }
                }
            }
        }
        else
        {
            $settingsConf   = ConfigurationUtil::getModuleConfiguration('settings');
            $emailSettings  = ArrayUtil::getValue($settingsConf, 'emailSettings' );
            if($emailSettings != null)
            {
                $emailSettings     = unserialize($emailSettings);
                $model->attributes = $emailSettings;
            }
        }
        $breadcrumbs      = [
                                [
                                    'label' => UsniAdaptor::t('settings', 'Email Settings')
                                ]
                            ];
        $this->getView()->params['breadcrumbs']  = $breadcrumbs;
        $viewHelper     = UsniAdaptor::app()->getModule('settings')->viewHelper;
        $view           = $viewHelper->getInstance('emailSettingsView', ['model' => $model]);
        $content        = $this->renderColumnContent([$view]);
        return $this->render($this->getDefaultLayout(), ['content' => $content]);
    }

    /**
     * Site settings
     * @return void
     */
    public function actionSite()
    {
        $modelClassName     = $this->resolveSiteSettingsFormClassName();
        $model              = new $modelClassName();
        if(isset($_POST['SiteSettingsForm']))
        {
            $model->attributes = $_POST['SiteSettingsForm'];
            $config = [
                                'model'             => $model,
                                'attribute'         => 'logo',
                                'uploadInstanceAttribute' => 'uploadInstance',
                                'type'              => 'image',
                                'savedAttribute'    => 'savedLogo',
                                'fileMissingError'  => UsniAdaptor::t('application', 'Please upload image'),
                          ];
            $uploadInstanceManager = new UploadInstanceManager($config);
            $result = $uploadInstanceManager->processUploadInstance();
            if($model->validate(null, false) && $result)
            {
                ConfigurationUtil::processInsertOrUpdateConfiguration($model, 'application', ['savedLogo', 'uploadInstance']);
                if(empty($model->errors))
                {
                    $uploadInstance     = UploadedFile::getInstance($model, 'logo');
                    $model->savedLogo   = ConfigurationUtil::getValue('application', 'logo');
                    if($uploadInstance != null)
                    {
                        $config = [
                                        'model'             => $model, 
                                        'attribute'         => 'logo', 
                                        'uploadInstance'    => $uploadInstance, 
                                        'savedFile'         => $model->savedLogo
                                  ];
                        FileUploadUtil::save('image', $config);
                    }
                    FlashUtil::setMessage('siteSettingsSaved', UsniAdaptor::t('settingsflash', 'Site settings are saved successfully.'));
                }
            }
        }
        else
        {
            $model->attributes = ConfigurationUtil::getModuleConfiguration('application');
        }
        $breadcrumbs      = [
                                [
                                    'label' => UsniAdaptor::t('settings', 'Site Settings')
                                ]
                            ];
        $this->getView()->params['breadcrumbs']  = $breadcrumbs;
        $viewHelper     = UsniAdaptor::app()->getModule('settings')->viewHelper;
        $view           = $viewHelper->getInstance('siteSettingsView', ['model' => $model]);
        $content        = $this->renderColumnContent([$view]);
        return $this->render($this->getDefaultLayout(), ['content' => $content]);
    }
    
    /**
     * Resolve site setting view class name.
     * @return string
     */
    protected function resolveSiteSettingsViewClassName()
    {
        return SiteSettingsView::className();
    }
    
    /**
     * Resolve site setting form class name.
     * @return string
     */
    protected function resolveSiteSettingsFormClassName()
    {
        return SiteSettingsForm::className();
    }
    
    /**
     * Menu settings.
     * @return void
     */
    public function actionMenu()
    {
        $model      = new MenuSettingsForm();
        if(isset($_POST['MenuSettingsForm']))
        {
            $model->attributes = $_POST['MenuSettingsForm'];
            if(!empty($model->sortOrder))
            {
                $model->sortOrder = serialize($model->sortOrder);
            }
            if($model->validate())
            {
                ConfigurationUtil::processInsertOrUpdateConfiguration($model, 'site');
                if(empty($model->errors))
                {
                    FlashUtil::setMessage('menuSettingsSaved', UsniAdaptor::t('settingsflash', 'Menu settings are saved successfully.'));
                }
            }
        }
        else
        {
            $model->attributes = ConfigurationUtil::getModuleConfiguration('site');
            $model->sortOrder  = $model->sortOrder;
        }
        $breadcrumbs      = [
                                [
                                    'label' => UsniAdaptor::t('settings', 'Menu Settings')
                                ]
                            ];
        $this->getView()->params['breadcrumbs']  = $breadcrumbs;
        $viewHelper     = UsniAdaptor::app()->getModule('settings')->viewHelper;
        $view           = $viewHelper->getInstance('menuSettingsView', ['model' => $model]);
        $content        = $this->renderColumnContent([$view]);
        return $this->render($this->getDefaultLayout(), ['content' => $content]);
    }

    /**
     * Get action to permission map.
     * @return array
     */
    protected function getActionToPermissionsMap()
    {
        return [
                    'menu'          => 'settings.menu',
                    'site'          => 'settings.site',
                    'email'         => 'settings.email',
                    'database'      => 'settings.database',
                    'module-settings'    => 'settings.module-settings'
               ];
    }

    /**
     * Resolve model class name for the controller.
     * @return null
     */
    protected function resolveModelClassName()
    {
        return null;
    }
    
    /**
     * Manage settings of modules.
     * @return void
     */
    public function actionModuleSettings()
    {
        $gridView       = $this->renderGridView();
        $content        = $this->renderColumnContent($gridView);
        return $this->render($this->getDefaultLayout(), ['content' => $content]);
    }
    
    /**
     * Change status for module settings.
     * @return void
     */
    public function actionChangeStatus()
    {
        $moduleMetadata = CacheUtil::get('moduleMetadata');
        //if not in cache
        if(empty($moduleMetadata))
        {
            $moduleMetadata = ConfigurationUtil::getValue('application', 'moduleMetadata');
        }
        $moduleMetadata  = Json::decode($moduleMetadata);
        $value           = $moduleMetadata[$_GET['id']];
        $value['status'] = $_GET['status'];
        $moduleMetadata[$_GET['id']] = $value;
        ConfigurationUtil::insertOrUpdateConfiguration('application', 'moduleMetadata', Json::encode($moduleMetadata));
        CacheUtil::clearCache();
        return $this->renderGridView();
    }
    
    /**
     * @inheritdoc
     */
    public function renderGridView($config = [])
    {
        $moduleMetadata = ConfigurationUtil::getValue('application', 'moduleMetadata');
        $moduleMetadata = Json::decode($moduleMetadata);
        $data           = [];
        foreach ($moduleMetadata as $key => $value)
        {
            if(in_array($key, $moduleMetadata) == false)
            {
                $value['id'] = $key;
                $data[]      = $value;
            }
        }
        $breadcrumbs      = [
                                [
                                    'label' => UsniAdaptor::t('settings', 'Module Settings')
                                ]
                            ];
        $this->getView()->params['breadcrumbs']  = $breadcrumbs;
        $dataProvider   = new ArrayDataProvider(['allModels' => $data, 'pagination' => ['pageSize' => 10], 'sort' => ['attributes' => ['id']]]);
        $gridView       = new ModuleSettingsView(['dataProvider' => $dataProvider]);
        return $gridView->render();
    }
    
    /**
     * Database settings.
     * @return void
     */
    public function actionDatabase()
    {
        $model      = new DatabaseSettingsForm();
        if(isset($_POST['DatabaseSettingsForm']))
        {
            $model->attributes = $_POST['DatabaseSettingsForm'];
            if($model->validate())
            {
                ConfigurationUtil::processInsertOrUpdateConfiguration($model, 'application');
                if(empty($model->errors))
                {
                    FlashUtil::setMessage('dbSettingsSaved', UsniAdaptor::t('settingsflash', 'Database settings are saved successfully.'));
                }
            }
        }
        else
        {
            $model->attributes = ConfigurationUtil::getModuleConfiguration('application');
        }
        $breadcrumbs      = [
                                [
                                    'label' => UsniAdaptor::t('application', 'Database Settings')
                                ]
                            ];
        $this->getView()->params['breadcrumbs']  = $breadcrumbs;
        $view       = new DatabaseSettingsView($model);
        $content    = $this->renderColumnContent([$view]);
        return $this->render($this->getDefaultLayout(), array('content' => $content));
    }
}