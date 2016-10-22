<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\UsniAdaptor;
/**
 * Top nav view for admin panel.
 * @package usni\library\views
 */
class UiTopNavView extends UiView
{
    /**
     * @return string
     */
    protected function renderContent()
    {
        return $this->getView()->renderPhpFile(UsniAdaptor::getAlias($this->resolveTopNavFile()) . '.php'); 
    }

    /**
     * Resolve top navigation file.
     * @return string
     */
    protected function resolveTopNavFile()
    {
        return '@usni/themes/bootstrap/views/site/_topnav';
    }
}
?>