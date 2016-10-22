<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\widgets;

/**
 * ButtonToolbar renders a button toolbar bootstrap component.
 *
 * For example,
 *
 * ```php
 * // a button toolbar group with items configuration
 * echo UiGridViewActionToolBar::widget([
 *     'model'         => $this->model,
 *     'controller'    => $this->getController(),
 *     'options'       => ['class' => 'action-toolbar'],
 *     'grid'          => $this
 * ]);
 * ```
 * @see usni\library\components\UiGridView
 * @see http://getbootstrap.com/components/#btn-groups-toolbar
 * @see http://getbootstrap.com/components/#btn-groups
 * @package usni\library\extensions\bootstrap\widgets
 */
class TranslatableGridViewActionToolBar extends UiGridViewActionToolBar
{
    /**
     * Get grid view action button group.
     * @return string
     */
    public function getGridViewActionButtonGroup()
    {
        if($this->gridViewActionButtonGroup == null)
        {
            return TranslatableGridViewActionButtonGroup::className();
        }
        return $this->gridViewActionButtonGroup;
    }
}
