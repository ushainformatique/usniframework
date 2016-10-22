<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\views;

use usni\library\extensions\bootstrap\views\UiBootstrapEditView;
use usni\library\views\UiTabbedView;
use usni\UsniAdaptor;
/**
 * UiTabbedEditView class file
 * @package usni\library\extensions\bootstrap\views
 */
abstract class UiTabbedEditView extends UiBootstrapEditView
{
    /**
     * @inheritdoc
     */
    protected function processRender()
    {
        $tabbedViewClassName = static::getTabbedViewClass();
        $tabbedView  = new $tabbedViewClassName($this->getTabs(), $this->getTabContainerHtmlOptions());
        return $tabbedView->render();
    }
    
    /**
     * Get tabbed view class
     * @return string
     */
    protected static function getTabbedViewClass()
    {
        return UiTabbedView::className();
    }

    /**
     * Get tabs.
     * @return array
     */
    abstract protected function getTabs();

    /**
     * Render tab elements.
     * @param string $tab
     * @return string
     */
    protected function renderTabElements($tab)
    {
        $content            = null;
        $elements           = $this->getTabElementsMap();
        $tabElements        = $elements[$tab];
        foreach($tabElements as $name)
        {
            $content .= $this->renderTabElement($tab, $name);
        }
        return $content;
    }

    /**
     * Get tab elements map.
     * @return array
     */
    abstract protected function getTabElementsMap();

    /**
     * Renders tab element.
     * @param string $tab
     * @param string $name
     * @return string
     */
    protected function renderTabElement($tab, $name)
    {
        $elementsOutputData = $this->getElementsOutputData();
        return $elementsOutputData[$name];
    }
    
    /**
     * Gets tab container html options.
     * @return array
     */
    protected function getTabContainerHtmlOptions()
    {
        return ['class' => 'nav nav-tabs'];
    }
    
    /**
     * @inheritdoc
     */
    public function shouldRenderErrorSummary()
    {
        return false;
    }
    
    /**
     * @inheritdoc
     */
    protected function registerScripts()
    {
        parent::registerScripts();
        $formId             = static::getFormId();
        $script             = "$('#{$formId}').on('afterValidate',
                                     function(event, jqXHR, settings)
                                     {
                                        var form = $(this);
                                        if(form.find('.has-error').length) {
                                            $('#formErrorsInfo').show();
                                            return false;
                                        }
                                        $('#formErrorsInfo').hide();
                                        return true;
                                     });";
        $this->getView()->registerJs($script);
    }
    
    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $content = parent::renderContent();
        $doesErrorExists = $this->model->hasErrors();
        if($doesErrorExists)
        {
            $style = "display:block";
        }
        else
        {
            $style = "display:none";
        }
        $formErrors = '<div class="alert alert-danger" id="formErrorsInfo" style="' . $style . '">' . 
                    UsniAdaptor::t('application', 'Please check the form carefully for the errors') . '</div>';
        return $formErrors . $content;
    }
}
?>