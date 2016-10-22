<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\views;

use usni\library\extensions\bootstrap\views\UiBootstrapModalEditView;
use usni\library\utils\AdminUtil;
/**
 * UiBootstrapQuickCreateModalEditView class file
 * @package usni\library\extensions\bootstrap\views
 */
abstract class UiBootstrapQuickCreateModalEditView extends UiBootstrapModalEditView
{
    /**
     * Get target element id.
     * @return null
     */
    protected static function getTargetElementId()
    {
        return null;
    }

    /**
     * Get modal id.
     * @return null|string
     */
    protected static function getModalId()
    {
        return 'quickCreateModal';
    }
    
    /**
     * Allow event before rendering content
     * @return boolean
     */
    protected function allowEventBeforeRenderingContent()
    {
        return false;
    }
    
    /**
     * @inheritdoc
     */
    protected function registerScripts()
    {
        $url                = static::getActionUrl();
        $formId             = static::getFormId();
        $targetDropDownId   = static::getTargetElementId();
        $modelClassName     = $this->getModelClassName();
        AdminUtil::addDropdownOptionScriptOnQuickCreate($url, $formId, $modelClassName, $targetDropDownId, $this->getView(), static::getModalId());
        AdminUtil::registerCancleModalViewScripts($this->getView(), static::getModalId());
    }
}