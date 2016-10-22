<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\components;

use usni\UsniAdaptor;
use usni\library\extensions\bootstrap\widgets\UiDataColumn;
use usni\library\components\UiHtml;

/**
 * AuthNameDataColumn class file.
 * @package usni\library\modules\auth\components
 */
class AuthNameDataColumn extends UiDataColumn
{
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $modelClassName  = UsniAdaptor::getObjectClassName($model);
        $controllerId    = lcfirst($modelClassName);
        return UiHtml::a(str_repeat('&nbsp;&nbsp;&nbsp;', $model->level) . $model->name, UsniAdaptor::createUrl("/auth/$controllerId/view", array("id" => $model->id )));
    }

}