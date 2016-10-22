<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\widgets;

use usni\UsniAdaptor;
use usni\library\extensions\bootstrap\widgets\UiDataColumn;
use usni\library\components\UiHtml;
/**
 * TreeModelNameDataColumn class file.
 * @package usni\library\modules\auth\components
 */
class TreeModelNameDataColumn extends UiDataColumn
{
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $modelClassName = UsniAdaptor::getObjectClassName($model);
        $controllerId   = UsniAdaptor::app()->controller->id;
        $moduleId       = UsniAdaptor::app()->controller->module->id;
        return UiHtml::a(str_repeat('&nbsp;&nbsp;&nbsp;', $model->level) . $model->name, UsniAdaptor::createUrl("/$moduleId/$controllerId/view", array("id" => $model->id )));
    }

}