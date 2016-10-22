<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\library\views\UiView;
use usni\library\extensions\bootstrap\widgets\UiTabs;

/**
 * Bootstrap tab view.
 * @package usni\library\views
 */
class UiTabbedView extends UiView
{
    /**
     * Html options for tab container.
     * @var array
     */
    public $tabContainerHtmlOptions = ['class' => 'nav nav-tabs'];
    /**
     * Html options for tab content.
     * @var array
     */
    public $wrapperOptions;
    /**
     * @var array tab definitions.
     * @see Tabs for the item details
     */
    public $tabs = array();

    /**
     * Class constructor.
     * @param array $tabs
     * @return void
     */
    public function __construct($tabs = [], $tabContainerHtmlOptions = [])
    {
        $this->tabs = $tabs;
        if(!empty($tabContainerHtmlOptions))
        {
            $this->tabContainerHtmlOptions = $tabContainerHtmlOptions;
        }
    }

    /**
     * Renders content.
     * @return string
     */
    protected function renderContent()
    {
        $widgetPath = $this->getTabWidgetPath();
        return $widgetPath::widget($this->getTabViewParams());
    }

    /**
     * Gets tab widget path.
     * @return string
     */
    protected function getTabWidgetPath()
    {
        return UiTabs::className();
    }

    /**
     * Get tab view params.
     * @return array
     */
    protected function getTabViewParams()
    {
        return [
                'items'                     => $this->tabs,
                'options'                   => $this->tabContainerHtmlOptions,
                'wrapperOptions'            => $this->getWrapperOptions(),
                'tabContentContainerHtmlOptions' => $this->getTabContentContainerHtmlOptions()
               ];
    }

    /**
     * Gets html options.
     * @return array
     */
    protected function getWrapperOptions()
    {
        return array('class' => 'tabbable');
    }
    
    /**
     * Gets html options for tab content container.
     * @return array
     */
    protected function getTabContentContainerHtmlOptions()
    {
        return array('class' => 'tab-content with-padding');
    }
}
?>