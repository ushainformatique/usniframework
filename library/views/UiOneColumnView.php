<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\UsniAdaptor;
/**
 * One column view for the application.
 * @package usni\library\views
 */
class UiOneColumnView extends UiColumnView
{
    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $topnav         = $this->renderTopnavbar();
        $content        = $this->renderBody();
        $breadcrumbs    = $this->renderBreadcrumb();
        $file           = UsniAdaptor::getAlias($this->getLayout());
        return $this->getView()->renderPhpFile($file,
                                                    array('topnav'          => $topnav,
                                                          'content'         => $content,
                                                          'breadcrumbs'     => $breadcrumbs,
                                                          'footer'          => $this->renderFooter()
                                                         ));
    }

    /**
     * Get layout for two column view
     * @return string
     */
    protected function getLayout()
    {
        return '@usni/themes/bootstrap/views/layouts/singlecolumn.php';
    }
}
?>