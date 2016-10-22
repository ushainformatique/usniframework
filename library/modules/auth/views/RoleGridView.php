<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\views;

use usni\library\components\UiGridView;
use usni\library\modules\auth\components\AuthActionColumn;
/**
 * RoleGridView class file
 * @package usni\library\modules\auth\views
 */
class RoleGridView extends UiGridView
{
    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        $columns = [
                      [
                          'attribute'   => 'name',
                          //'value'       => '$data->renderNodeWithParents()', 'type' => 'html'
                      ],
                      [
                         'class'        => AuthActionColumn::className(),
                         'template'     => '{view} {update} {delete}, {managepermissions}'
                      ]
                   ];
        return $columns;
    }
}
?>