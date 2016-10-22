<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\views;

use usni\library\views\UiDetailView;
use usni\library\utils\StatusUtil;
use usni\library\modules\auth\views\GroupBrowseModelView;
/**
 * Group Detail View.
 *
 * @package usni\library\modules\auth\views
 */
class GroupDetailView extends UiDetailView
{
    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        return [
                    ['attribute' => 'status', 'value' => StatusUtil::renderLabel($this->model), 'format' => 'raw'],
                    ['attribute' => 'parent_id', 'value' => $this->model->getParentName($this->model, null, null, null)],
                    ['attribute' => 'member', 'value' => $this->model->getGroupMembers()]
               ];
    }

    /**
     * @inheritdoc
     */
    protected function getTitle()
    {
        return $this->model->name;
    }
    
    /**
     * @inheritdoc
     */
    protected static function resolveBrowseModelViewClassName()
    {
        return GroupBrowseModelView::className();
    }
}
