<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\home\controllers;
use usni\library\components\UiAdminController;
use usni\UsniAdaptor;
use usni\library\modules\home\views\DashboardView;
use usni\library\utils\ArrayUtil;
/**
 * DefaultController for the module.
 * @package usni\library\modules\home\controllers
 */
class DefaultController extends UiAdminController
{
    /**
     * Gets non permissible actions.
     * @return string
     */
    protected static function getNonPermissibleActions()
    {
        $actions = parent::getNonPermissibleActions();
        return ArrayUtil::merge($actions, ['index', 'dashboard']);
    }

    /**
     * Renders to Login or dashboard.
     * @return void
     */
    public function actionIndex()
    {
        if (UsniAdaptor::app()->user->getIsGuest())
        {
            UsniAdaptor::app()->user->loginRequired();
        }
        else
        {
            $this->redirect(UsniAdaptor::createUrl('/home/default/dashboard'));
        }
    }

    /**
     * Loads dashboard.
     * @return void
     */
    public function actionDashboard()
    {
        if (UsniAdaptor::app()->user->getIsGuest())
        {
            UsniAdaptor::app()->user->loginRequired();
        }
        else
        {
            $this->getView()->params['breadcrumbs'] = [UsniAdaptor::t('application', 'Dashboard')];
            $view               = $this->getColumnView();
            $view->addContainedView(new DashboardView());
            $content            = $view->render();
            return $this->render($this->getDefaultLayout(), ['content' => $content]);
        }
    }

    /**
     * Resolves model class name.
     * @return null
     */
    protected function resolveModelClassName()
    {
        return null;
    }

    /**
     * Get page titles.
     * @return array
     */
    public function pageTitles()
    {
        return [
                    'index'     => UsniAdaptor::t('application', 'Dashboard'),
                    'dashboard' => UsniAdaptor::t('application', 'Dashboard')
               ];
    }

    /**
     * Get action to permission map.
     * @return array
     */
    /*protected function getActionToPermissionsMap()
    {
        return array(
            'dashboard' => 'home.dashboard',
            'index'     => 'home.dashboard'
        );
    }*/
}
?>