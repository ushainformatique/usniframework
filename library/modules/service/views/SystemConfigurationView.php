<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\service\views;

use usni\library\modules\install\views\InstallCheckSystemView;
/**
 * SystemConfigurationView class file.
 * @package usni\library\modules\service\views
 */
class SystemConfigurationView extends InstallCheckSystemView
{
    /**
     * Gets view file.
     * @return string
     */
    protected function getViewFile()
    {
        return '@usni/themes/bootstrap/views/services/settings';
    }
}
?>