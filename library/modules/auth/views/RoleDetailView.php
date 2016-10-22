<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\views;

use usni\library\views\UiDetailView;
use usni\library\utils\StatusUtil;
/**
 * RoleDetailView class file
 * @package usni\library\modules\auth\views
 */
class RoleDetailView extends UiDetailView
{
    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        return [
                    'name',
                    [
                        'attribute' => 'status', 'value' => StatusUtil::renderLabel($this->model), 'format' => 'raw'
                    ],
                    'parent_id'
               ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getTitle()
    {
        return $this->model->name;
    }
}
