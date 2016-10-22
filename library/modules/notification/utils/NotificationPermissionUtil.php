<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\utils;

use usni\library\utils\PermissionUtil;
use usni\library\modules\notification\models\Notification;
use usni\library\modules\notification\models\NotificationTemplate;
use usni\library\modules\notification\models\NotificationLayout;
/**
 * NotificationPermissionUtil class file.
 * @package usni\library\modules\notification\utils
 */
class NotificationPermissionUtil extends PermissionUtil
{
    /**
     * @inheritdoc
     */
    public static function getModels()
    {
        return [Notification::className(), NotificationTemplate::className(), NotificationLayout::className()];
    }

    /**
     * @inheritdoc
     */
    public static function getModuleId()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public static function getModelToExcludedPermissions()
    {
         return [Notification::className() => ['create', 'update', 'delete', 'view', 'bulkedit', 'bulkdelete', 'updateother', 'viewother', 'deleteother']];
    }
}
?>