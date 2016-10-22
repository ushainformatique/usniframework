<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\widgets;

use usni\library\extensions\bootstrap\widgets\UiDataColumn;
use usni\UsniAdaptor;
use usni\library\components\UiHtml;
/**
 * UserNameDataColumn class file.
 * @package usni\library\modules\users\widgets
 */
class UserNameDataColumn extends UiDataColumn
{
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return UiHtml::a($model->username, UsniAdaptor::createUrl("users/default/view", ["id" => $model->id]), ['target' => '_blank', 'data-pjax' => 0]);
    }

}
