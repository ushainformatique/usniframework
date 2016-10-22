<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\UsniAdaptor;
/**
 * Side navigation view.
 *
 * @package usni.library.views
 */
class UiSideNavView extends UiView
{
    /**
     * Renders content.
     * @return string
     */
    protected function renderContent()
    {
        $menuView   = UsniAdaptor::app()->viewHelper->getInstance('menuView');
        $content    = $menuView->render();
        return $this->getView()->renderPhpFile(UsniAdaptor::getAlias('@usni/themes/bootstrap/views/site/_sidenav') . '.php', array('content' => $content));
    }
}
?>