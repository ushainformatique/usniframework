<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\library\views\UiView;
/**
 * UiAdminViewHelper class file.
 * 
 * @package usni\library\components
 */
class UiAdminViewHelper extends BaseViewHelper
{
    public $columnView      = 'usni\library\views\UiTwoColumnView';

    public $topNavView      = 'usni\library\views\UiTopNavView';

    public $breadcrumbView  = 'usni\library\views\UiBreadCrumbView';

    public $sidenavView     = 'usni\library\views\UiSideNavView';

    public $menuView        = 'usni\library\views\UiMenuView';

    /**
     * Get column content.
     * @param array $inputViews
     * @return string
     */
    public function renderColumnContent($inputViews)
    {
        $columnView = $this->getInstance('columnView');
        if(is_string($inputViews))
        {
            $columnView->addContainedView($inputViews);
        }
        if(is_object($inputViews) && $inputViews instanceof UiView)
        {
            $columnView->addContainedView($inputViews);
        }
        if(is_array($inputViews))
        {
            foreach ($inputViews as $inputView)
            {
                $columnView->addContainedView($inputView);
            }
        }
        return $columnView->render();
    }
}