<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\install\controllers;

use yii\web\BadRequestHttpException;
use usni\library\components\UiAdminController;
use usni\UsniAdaptor;
use usni\library\components\UiRequirementChecker;
use usni\library\modules\install\views\InstallWelcomeView;
use usni\library\modules\install\views\InstallCheckSystemView;
use usni\library\views\UiOneColumnView;
use yii\web\Response;
use usni\library\modules\install\components\InstallManager;
use usni\library\modules\install\models\SettingsForm;
use usni\library\modules\install\views\InstallSettingsView;
use usni\library\modules\install\views\InstallFinishView;
use usni\library\components\OutputBufferStreamer;
use usni\library\components\UiHtml;
use yii\web\UploadedFile;
use usni\library\utils\FileUploadUtil;
use usni\library\utils\FileUtil;
/**
 * DefaultController for the install module.
 * 
 * @package usni\library\modules\install\controllers
 */
class DefaultController extends UiAdminController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [];
    }

    /**
     * Runs the installation.
     * @return void
     */
    public function actionIndex()
    {
        $columnViewClassName = $this->resolveColumnViewClassName();
        UsniAdaptor::app()->viewHelper->columnView = new $columnViewClassName();
        UsniAdaptor::app()->user->switchIdentity(null);
        UsniAdaptor::app()->getSession()->destroy();
        $viewClass  = $this->getInstallWelcomeView();
        $view       = new $viewClass();
        $content    = $this->renderColumnContent($view);
        return $this->render($this->getDefaultLayout(), array('content' => $content));
    }
    
    /**
     * Gets install welcome view.
     * @return string
     */
    protected function getInstallWelcomeView()
    {
        return InstallWelcomeView::className();
    }

    /**
     * Checks system.
     * @return void
     */
    public function actionCheckSystem()
    {
        $columnViewClassName = $this->resolveColumnViewClassName();
        UsniAdaptor::app()->viewHelper->columnView = new $columnViewClassName();
        $requirementsChecker = new UiRequirementChecker();
        $requirements        = $requirementsChecker->getApplicationRequirements();
        $systemResults       = $requirementsChecker->checkYii()->check($requirements)->getResult();
        $view                = new InstallCheckSystemView($systemResults);
        $content             = $this->renderColumnContent($view);
        return $this->render($this->getDefaultLayout(), array('content' => $content));
    }

    /**
     * Load settings.
     * @return void
     */
    public function actionSettings()
    {
        $columnViewClassName = $this->resolveColumnViewClassName();
        UsniAdaptor::app()->viewHelper->columnView = new $columnViewClassName();
        $model      = new SettingsForm(['scenario' => 'create']);
        if(UsniAdaptor::app()->request->isAjax)
        {
            $model->attributes = $_POST['SettingsForm'];
            UsniAdaptor::app()->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if(isset($_POST['SettingsForm']))
        {
            $model->attributes  = $_POST['SettingsForm'];
            $uploadInstance     = UploadedFile::getInstance($model, 'logo');
            if($uploadInstance != null)
            {
                $model->logo = FileUploadUtil::getEncryptedFileName($uploadInstance->name);
            }
            if($uploadInstance != null)
            {
                $config = [
                            'model'             => $model, 
                            'attribute'         => 'logo', 
                            'uploadInstance'    => $uploadInstance, 
                            'savedFile'         => null,
                            'thumbWidth'        => 500,
                            'thumbHeight'       => 500
                          ];
                FileUploadUtil::save('image', $config);
            }
            if($model->validate())
            {
                $runTimePath = UsniAdaptor::app()->getRuntimePath();
                $data   = UsniAdaptor::app()->security->hashData(serialize($model->getAttributes()), InstallManager::INSTALL_KEY);
                $value  = base64_encode($data);
                FileUtil::createDirectory(FileUtil::normalizePath($runTimePath. DS . 'install'));
                FileUtil::writeFile(FileUtil::normalizePath($runTimePath. DS . 'install'), 'settingsdata.bin', 'wb', $value);
                $url    = UsniAdaptor::createUrl('/install/default/run-installation');
                $this->redirect($url);
            }
        }
        $viewClassName  = $this->getInstallSettingsView();
        $view           = new $viewClassName($model);
        $content        = $this->renderColumnContent([$view]);
        return $this->render($this->getDefaultLayout(), ['content' => $content]);
    }
    
    /**
     * Gets install settings view.
     * @return string
     */
    protected function getInstallSettingsView()
    {
        return InstallSettingsView::className();
    }

    /**
     * Runs installtion.
     * @param string $settings
     * @return void
     */
    public function actionRunInstallation()
    {
        $settings               = file_get_contents(FileUtil::normalizePath(UsniAdaptor::app()->getRuntimePath() . DS . 'install' . DS .  'settingsdata.bin'));
        $columnViewClassName    = $this->resolveColumnViewClassName();
        UsniAdaptor::app()->viewHelper->columnView = new $columnViewClassName();
        $model              = new SettingsForm(['scenario' => 'create']);
        if(($data = base64_decode($settings)) !== false)
        {
            if(($data = UsniAdaptor::app()->security->validateData($data, InstallManager::INSTALL_KEY)) !== false)
            {
                $data = unserialize($data);
            }
            else
            {
                throw new BadRequestHttpException();
            }
        }
        $model->attributes  = $data;
        $viewClassName      = $this->getInstallFinishView();
        $view               = new $viewClassName();
        $content            = $this->renderColumnContent($view);
        //Not very clear why? but it works and resolve unable to find debug data issue
        if (class_exists('yii\debug\Module')) 
        {
            UsniAdaptor::app()->getView()->off(\yii\web\View::EVENT_END_BODY, [\yii\debug\Module::getInstance(), 'renderToolbar']);
        }
        echo $this->render($this->getDefaultLayout(), array('content' => $content));
        $installManager     = UsniAdaptor::app()->installManager;
        $template           = UiHtml::script("$('#progress-messages').prepend('{message}<br/>');");
        $progressTemplate   = UiHtml::script("$('.install-progress').html('{message}%');
                                             $('.progress-bar').attr('aria-valuenow', '{message}');
                                             $('.progress-bar').attr('style', 'width:{message}%');");
        $obRenderer         = new OutputBufferStreamer($template, $progressTemplate);
        $installManager->runInstallation($model, $obRenderer, 'instance.php');
    }
    
    /**
     * Gets install finish view.
     * @return string
     */
    protected function getInstallFinishView()
    {
        return InstallFinishView::className();
    }

    /**
     * Resolve default inner content column view.
     * @return string
     */
    protected function resolveDefaultInnerContentColumnView()
    {
        return UsniAdaptor::app()->viewHelper->oneColumnView;
    }
    
    /**
     * Get default layout.
     * @return string
     */
    protected function getDefaultLayout()
    {
        return '@usni/themes/bootstrap/views/layouts/install';
    }
    
    /**
     * Resolve column view class name
     * @return string
     */
    protected function resolveColumnViewClassName()
    {
        return UiOneColumnView::className();
    }
}