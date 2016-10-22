<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\components;

use usni\UsniAdaptor;
use usni\library\extensions\bootstrap\widgets\UiDataColumn;
use usni\library\components\UiHtml;

/**
 * NotificationLayoutNameDataColumn class file.
 * @package usni\library\modules\notification\components
 */
class NotificationLayoutNameDataColumn extends UiDataColumn
{
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return UiHtml::a($model->name, UsniAdaptor::createUrl("notification/layout/view", ["id" => $model->id]));
    }

}