<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\components;

use usni\library\extensions\bootstrap\widgets\UiDataColumn;
use usni\library\modules\notification\utils\NotificationUtil;

/**
 * NotificationStatusDataColumn class file.
 * 
 * @package usni\library\modules\notification\components
 */
class NotificationStatusDataColumn extends UiDataColumn
{
    /**
     * Renders the data cell content.
     * This method evaluates {@link value} or {@link name} and renders the result.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return NotificationUtil::renderLabel($model);
    }
}