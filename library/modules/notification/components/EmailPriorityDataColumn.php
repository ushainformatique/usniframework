<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\components;

use usni\library\extensions\bootstrap\widgets\UiDataColumn;
use usni\library\modules\notification\utils\NotificationUtil;
use usni\UsniAdaptor;
use usni\library\extensions\bootstrap\widgets\UiLabel;
use usni\library\components\UiHtml;
/**
 * EmailPriorityDataColumn class file.
 * 
 * @package usni\library\modules\notification\components
 */
class EmailPriorityDataColumn extends UiDataColumn
{
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = NotificationUtil::getPriorityDisplayLabel($model->priority);
         if ($value == UsniAdaptor::t('notification', 'High'))
         {
             return UiLabel::widget(['content' => $value, 'modifier' => UiHtml::COLOR_SUCCESS]);
         }
        elseif ($value == UsniAdaptor::t('notification', 'Medium'))
        {
            return UiLabel::widget(['content' => $value, 'modifier' => UiHtml::COLOR_WARNING]);
        }
        elseif ($value == UsniAdaptor::t('notification','Low'))
        {
            return UiLabel::widget(['content' => $value, 'modifier' => UiHtml::COLOR_DANGER]);
        }
        else
        {
            return $value;
        }
    }

}
