<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */

namespace usni\library\modules\auth\controllers;

use usni\library\components\UiAdminController;
use usni\UsniAdaptor;
/**
 * GroupController class file.
 *
 * @package usni\library\modules\auth\controllers
 */
class DefaultController extends UiAdminController
{
    /**
     * Redirects to group manage. Index action will be invoked if there is no any action found in url. eg: '/index.php/auth'
     * @return void
     */
    public function actionIndex()
    {
        $this->redirect(UsniAdaptor::createUrl('auth/group/manage'));
    }
}
