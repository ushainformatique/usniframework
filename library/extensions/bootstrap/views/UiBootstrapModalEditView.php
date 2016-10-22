<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\views;

use usni\library\extensions\bootstrap\views\UiBootstrapEditView;
/**
 * UiBootstrapModalEditView class file
 *
 * @package usni\library\extensions\bootstrap\views
 */
abstract class UiBootstrapModalEditView extends UiBootstrapEditView
{
    /**
     * Get action url.
     * @throws MethodNotImplementedException
     */
    protected static function getActionUrl()
    {
        throw new MethodNotImplementedException();
    }

    /**
     * Resolve form view path.
     * @return string
     */
    public function resolveFormViewPath()
    {
        return '@usni/themes/bootstrap/views/site/_modalform';
    }

    /**
     * Get buttons wrapper.
     * @return string
     */
    protected function getButtonsWrapper()
    {
        return "<div class='col-sm-offset-4 col-sm-8'>{buttons}</div>";
    }

    /**
     * Get modal id.
     * @return null|string
     */
    protected static function getModalId()
    {
        throw new MethodNotImplementedException();
    }

    /**
     * Get model class name.
     * @return null|string
     */
    protected static function getModelClassName()
    {
        throw new MethodNotImplementedException();
    }

    /**
     * @inheritdoc
     */
    public function resolveOutputData()
    {
        $output = parent::resolveOutputData();
        return array_merge($output, ['modalId' => static::getModalId(), 'modalSize' => static::getModalSize()]);
    }
    
    /**
     * Get modal size
     * @return string
     */
    protected static function getModalSize()
    {
       return ""; 
    }
}