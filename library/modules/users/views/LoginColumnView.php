<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\views\UiColumnView;
/**
 * One column view for the application.
 * @package usni\library\views
 */
class LoginColumnView extends UiColumnView
{
    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $topnav         = $this->renderTopnavbar();
        $content        = $this->renderBody();
        return $topnav . $content . $this->renderFooter();
//        $file           = UsniAdaptor::getAlias($this->getLayout());
//        return $this->getView()->renderPhpFile($file,
//                                                    array('topnav'          => $topnav,
//                                                          'content'         => $content,
//                                                          'breadcrumbs'     => $breadcrumbs,
//                                                          'footer'          => $this->renderFooter()
//                                                         ));
    }

    /**
     * Get layout for two column view
     * @return string
     */
//    protected function getLayout()
//    {
//        return '@usni/themes/bootstrap/views/layouts/singlecolumn.php';
//    }
}
?>