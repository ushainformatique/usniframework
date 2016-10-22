<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\controllers;

use usni\library\components\UiAdminController;
use usni\library\modules\notification\models\Notification;
use usni\UsniAdaptor;
/**
 * DefaultController class file.
 * @package usni\library\modules\notification\controllers
 */
class DefaultController extends UiAdminController
{
    /**
     * @inheritdoc
     */
    protected function resolveModelClassName()
    {
        return Notification::className();
    }

    /**
     * @inheritdoc
     */
    protected function getActionToPermissionsMap()
    {
        return array('manage'           => 'notification.manage',
                     'gridViewSettings' => 'notification.manage');
    }
    
    /**
     * @inheritdoc
     */
    public function pageTitles()
    {
        return array(
            'manage'         => UsniAdaptor::t('application','Manage') . ' ' . Notification::getLabel(2),
        );
    }
}
?>