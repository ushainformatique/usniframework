<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\views;

use usni\library\views\UiBrowseModelView;
/**
 * GroupBrowseModelView class file
 * @package productCategories\views
 */
class GroupBrowseModelView extends UiBrowseModelView
{
    /**
     * @inheritdoc
     */
    protected function resolveDropdownData()
    {
        return $this->model->getMultiLevelSelectOptions($this->attribute, 0, '-', false, $this->shouldRenderOwnerCreatedModelsForBrowse);
    }
}
?>