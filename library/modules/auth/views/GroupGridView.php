<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */

namespace usni\library\modules\auth\views;

use usni\library\components\TranslatableGridView;
use usni\UsniAdaptor;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\modules\auth\components\AuthActionColumn;
use usni\library\widgets\UiStatusDataColumn;
use usni\library\modules\auth\components\AuthNameDataColumn;
use usni\library\modules\auth\models\Group;
use usni\library\components\Sort;
/**
 * Group grid view.
 * 
 * @package usni\library\modules\auth\views
 */
class GroupGridView extends TranslatableGridView
{
    /**
     * Get view columns.
     * @return array
     */
    public function getColumns()
    {
        $columns = [
            [
                'attribute' => 'name',
                'class'     => AuthNameDataColumn::className()
            ],
            [
                'attribute' => 'parent_id',
                'value'     => [$this->model, 'getParentName'],
                'filter'    => $this->model->getParentFilterDropdown()
            ],
            [
                'attribute' => 'status',
                'class'     => UiStatusDataColumn::className(),
                'filter'    => Group::getStatusDropdown()
            ],
            [
                'attribute' => 'level',
                'filter'    => $this->model->getLevelFilterDropdown(),
                'headerOptions' => ['class' => 'sort-numerical']
            ],
            [
                 'class'     => $this->resolveActionColumnClassName(),
                 'template'  => '{view} {update} {delete} {managepermissions}'
            ]
        ];
        return $columns;
    }

    /**
     * Resolve action button visiblity for manage permissions.
     * @param int $row
     * @param Model $model
     * @param string $buttonId
     * @return boolean
     */
    protected function resolveManagePermissionsVisibility($row, $model, $buttonId)
    {
        $user           = UsniAdaptor::app()->user->getUserModel();
        if(AuthManager::checkAccess($user, 'auth.managepermissions'))
        {
            return true;
        }
        return false;
    }
    
    /**
     * @inheritdoc
     */
    protected function resolveDataProviderSort()
    {
        return [
                    'class' => Sort::className()
               ];
    }
    
    /**
     * Resolve action column class name.
     * @return string
     */
    protected function resolveActionColumnClassName()
    {
        return AuthActionColumn::className();
    }
}