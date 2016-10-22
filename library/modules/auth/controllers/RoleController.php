<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\controllers;

use usni\library\components\UiAdminController;
use usni\library\modules\auth\models\Role;
/**
 * RoleController class file.
 * @package usni\library\modules\auth\controllers
 */
class RoleController extends UiAdminController
{
    /**
     * @inheritdoc
     */
    protected function resolveModelClassName()
    {
        return Role::className();
    }

}
