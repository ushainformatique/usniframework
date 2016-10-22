<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\service\controllers;

use usni\library\components\UiAdminController;
use usni\UsniAdaptor;
use usni\library\modules\service\views\IndexView;
use usni\library\modules\service\views\MigrationView;
use usni\library\modules\service\views\SystemConfigurationView;
use usni\library\modules\service\views\DataManagerOutputView;
use usni\library\modules\service\views\RebuildApplicationView;
use usni\library\modules\install\components\InstallManager;
use usni\library\components\UiRequirementChecker;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\utils\ArrayUtil;
use usni\library\utils\FileUtil;
use usni\library\utils\FlashUtil;
use usni\library\views\UiOneColumnView;
use usni\library\utils\ApplicationUtil;
/**
 * DefaultController for system.
 * 
 * @package usni\library\modules\service\controllers
 */
class DefaultController extends UiAdminController
{
    /**
     * Loads the index page for the settings controller.
     * @return void
     */
    public function actionIndex()
    {
        $this->getView()->params['breadcrumbs']  = [UsniAdaptor::t('application', 'Services')];
        $view               = new IndexView();
        $content            = $this->renderColumnContent(array($view));
        return $this->render($this->getDefaultLayout(), array('content' => $content));
    }

    /**
     * Runs the migration.
     * @return void
     */
    public function actionMigrate()
    {
        $this->runMigrationTool();
    }

    /**
     * Runs the migration tool.
     * @return void
     */
    private function runMigrationTool()
    {
        $this->getView()->params['breadcrumbs'] = [
                                                    ['label' => UsniAdaptor::t('service', 'Services'),
                                                     'url'   => UsniAdaptor::createUrl('service/default/index')],
                                                    ['label' => UsniAdaptor::t('service', 'Run Migration')]
                                                  ];
        $view    = new MigrationView();
        $content = $this->renderColumnContent(array($view));
        return $this->render($this->getDefaultLayout(), array('content' => $content));
    }

    /**
     * Checks system.
     * @return void
     */
    public function actionCheckSystem()
    {
        $this->getView()->params['breadcrumbs'] = [
                                                    ['label' => UsniAdaptor::t('service', 'Services'),
                                                     'url'   => UsniAdaptor::createUrl('service/default/index')],
                                                    ['label' => UsniAdaptor::t('service', 'System Configuration')]
                                                  ];
        $requirementsChecker = new UiRequirementChecker();
        $requirements        = $requirementsChecker->getApplicationRequirements();
        $systemResults       = $requirementsChecker->checkYii()->check($requirements)->getResult();
        $view               = new SystemConfigurationView($systemResults);
        $content = $this->renderColumnContent(array($view));
        return $this->render($this->getDefaultLayout(), array('content' => $content));
    }

    /**
 	 * Run the data manager.
 	 * @return void
 	 */
	public function actionRunDataManager($prefix)
	{
        $this->getView()->params['breadcrumbs'] = [
                                                    ['label' => UsniAdaptor::t('application', 'Services'),
                                                     'url'   => UsniAdaptor::createUrl('service/default/index')],
                                                    ['label' => UsniAdaptor::t('application', 'Run Data Manager')]
                                                  ];
        $view               = new DataManagerOutputView($prefix);
    	$content        	= $this->renderColumnContent(array($view));
        $this->render($this->getDefaultLayout(), array('content' => $content));
	}

    /**
     * Rebuild the module permissions.
     * @return void
     */
    public function actionLoadModulesPermissions()
    {
        ini_set('max_execution_time', 180);
        $message = UsniAdaptor::t('auth', 'Rebuild Permissions');
        if(AuthManager::addModulesPermissions())
        {
            FlashUtil::setMessage('serviceexecutionsuccess', UsniAdaptor::t('serviceflash', '{service} execution is successfull.', ['service' => $message]));
        }
        else
        {
            FlashUtil::setMessage('serviceexecutionsuccess', UsniAdaptor::t('serviceflash', '{service} execution fails.', ['service' => $message]));
        }
        return $this->redirect(UsniAdaptor::createUrl('service/default/index'));
    }

    /**
     * @inheritdoc
     */
    protected function resolveModelClassName()
    {
        return null;
    }

    /**
     * Checks system.
     * @return void
     */
    public function actionResetUserPermissions()
    {
        UsniAdaptor::app()->user->setUserPermissions(null);
        UsniAdaptor::app()->user->getUserPermissions();
        FlashUtil::setMessage('resetuserpermissions', 'User permissions reset successfully.');
        return $this->redirect(UsniAdaptor::createUrl('service/default/index'));
    }

    /**
     * Reload install time data based on settings.
     * @return void
     */
    public function actionReloadInstallData()
    {
        //Sets time limit
        @set_time_limit(1800);
        if(file_exists(UsniAdaptor::app()->getRuntimePath() . '/apploaded.bin'))
        {
            unlink(UsniAdaptor::app()->getRuntimePath() . '/apploaded.bin');
        }
        FileUtil::writeFile(UsniAdaptor::app()->getRuntimePath(), 'rebuildstate.bin', 'wb', 'Rebuild in progress');
        InstallManager::reloadInstallData();
        echo "Success";
        unlink(UsniAdaptor::app()->getRuntimePath() . '/rebuildstate.bin');
        FileUtil::writeFile(UsniAdaptor::app()->getRuntimePath(), 'apploaded.bin', 'wb', 'Application is loaded');
    }

    /**
     * Rebuild site.
     */
    public function actionRebuild()
    {
        UsniAdaptor::app()->viewHelper->columnView = new UiOneColumnView();
        $view       = new RebuildApplicationView();
        $content    = $this->renderColumnContent(array($view));
        return $this->render('@usni/themes/bootstrap/views/layouts/install', array('content' => $content));
    }
    
    /**
     * Clear assets
     * @return void
     */
    public function actionClearAssets()
    {
        FileUtil::clearAssets();
        return $this->redirect(UsniAdaptor::createUrl('service/default/index'));
    }

    /**
     * @inheritdoc
     */
    protected function getActionToPermissionsMap()
    {
        return array('index'                     => 'access.service',
                     'migrate'                   => 'service.migrate',
                     'checkSystem'               => 'service.checksystem',
                     'runDataManager'            => 'service.rundatamanager',
                     'loadModulesPermissions'    => 'service.loadmodulespermissions',
                     'resetUserPermissions'      => 'service.resetuserpermissions');
    }

    /**
     * @inheritdoc
     */
    protected static function getNonPermissibleActions()
    {
        $actions = parent::getNonPermissibleActions();
        return ArrayUtil::merge($actions, ['reload-install-data', 'rebuild']);
    }
    
    /**
     * @inheritdoc
     */
    public function pageTitles()
    {
        return [
            'index' => UsniAdaptor::t('application', 'Manage') . ' ' . UsniAdaptor::t('service', 'Services'),
        ];
    }
    
    /**
     * Rebuild module metadata
     * @return void
     */
    public function actionRebuildModuleMetadata()
    {
        ApplicationUtil::rebuildModuleMetadata();
        $message    = UsniAdaptor::t('service', 'Rebuild module metadata');
        FlashUtil::setMessage('serviceexecutionsuccess', UsniAdaptor::t('serviceflash', '{service} execution is successfull.', ['service' => $message]));
        return $this->redirect(UsniAdaptor::createUrl('service/default/index'));
    }
}
?>