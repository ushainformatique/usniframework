<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\views;

use usni\library\utils\AdminUtil;
use usni\UsniAdaptor;
/**
 * AdminMenuSettingsView class file.
 * @package usni\library\modules\settings\views
 */
class AdminMenuSettingsView extends MenuSettingsView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $metadata = parent::getFormBuilderMetadata();
        unset($metadata['elements']['itemClass']);
        unset($metadata['elements']['containerClass']);
        return $metadata;
    }

    /**
     * @inheritdoc
     */
    protected function renderTitle()
    {
        return UsniAdaptor::t('settings', 'Admin Menu Settings');
    }

    /**
     * Gets menu list items.
     * @throws MethodNotImplementedException
     * @return array
     */
    protected function getListItems()
    {
        $sortOrderData = unserialize($this->model->sortOrder);
        return AdminUtil::getAdminMenuItemsList($sortOrderData);
    }
}
?>