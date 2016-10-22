<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\views;

use usni\library\components\TranslatableGridView;
use usni\library\extensions\bootstrap\widgets\UiActionColumn;
use usni\library\modules\notification\components\NotificationLayoutNameDataColumn;
/**
 * NotificationLayoutGridView class file
 * @package usni\library\modules\notification\views
 */
class NotificationLayoutGridView extends TranslatableGridView
{
    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        $columns = [
                        [
                            'attribute'  => 'name',
                            'class'      => NotificationLayoutNameDataColumn::className()
                        ],
                        [
                            'class'      => UiActionColumn::className(),
                            'template'   => '{view} {update} {delete}'
                        ]
                   ];
        return $columns;
    }
    
    /**
     * @inheritdoc
     */
    protected static function getActionToolbarOptions()
    {
        $options                    = parent::getActionToolbarOptions();
        $options['showBulkEdit']    = false;
        return $options;
    }
}
?>