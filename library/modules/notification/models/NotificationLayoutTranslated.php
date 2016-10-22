<?php 
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\models;
    
use usni\library\components\UiSecuredActiveRecord;

/**
 * NotificationLayoutTranslated class file
 * @package usni\library\modules\notification\models
 */
class NotificationLayoutTranslated extends UiSecuredActiveRecord
{
    /**
     * @inheritdoc
     */
    public function getNotificationLayout()
    {
        return $this->hasOne(NotificationLayout::className(), ['id' => 'owner_id']);
    }
}
?>