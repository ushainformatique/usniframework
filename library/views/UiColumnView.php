<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\UsniAdaptor;
use usni\library\utils\ArrayUtil;
/**
 * Default view for the application.
 * 
 * @package usni\library\views
 */
class UiColumnView extends UiView
{
    /**
     * Contained views in the column.
     * @var array
     */
    protected $containedViews = array();

    /**
     * Adds a view to the column.
     * @param UiView $view
     */
    public function addContainedView($view)
    {
        $this->containedViews[] = $view;
    }

    /**
     * @return string
     */
    protected function renderContent()
    {
        return $this->renderBody();
    }

    /**
     * Renders body.
     * @return string
     */
    protected function renderBody()
    {
        $content = null;
        $views = $this->containedViews;
        foreach($views as $view)
        {
            if(is_string($view))
            {
                $content .= $view;
            }
            elseif($view instanceof UiView)
            {
                $content .= $view->render();
            }
        }
        return $content;
    }

    /**
     * Renders top navigation bar.
     * @return string
     */
    protected function renderTopnavbar()
    {
        $topnavView = UsniAdaptor::app()->viewHelper->getInstance('topNavView');
        return $topnavView->render();
    }

    /**
     * Renders breadcrumb.
     * @return string
     */
    protected function renderBreadcrumb()
    {
        $content = null;
        //Set the breadcrumb if there
        $breadcrumbs = ArrayUtil::getValue($this->getView()->params, 'breadcrumbs');
        if(!empty($breadcrumbs))
        {
            $breadcrumbView = UsniAdaptor::app()->viewHelper->getInstance('breadcrumbView');
            $content        = $breadcrumbView->render();
        }
        return $content;
    }

    /**
     * Renders footer.
     *
     * @return string
     */
    protected function renderFooter()
    {
        return $this->getView()->renderPhpFile(UsniAdaptor::getAlias('@usni/themes/bootstrap/views/site/_footer') . '.php', array());
    }
}
?>