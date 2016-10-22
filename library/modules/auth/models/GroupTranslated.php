<?php 
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\models;
    
use usni\library\components\UiSecuredActiveRecord;

/**
 * GroupTranslated class file
 * @package usni\library\modules\auth\models
 */
class GroupTranslated extends UiSecuredActiveRecord
{
    /**
     * @inheritdoc
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'owner_id']);
    }
}
?>