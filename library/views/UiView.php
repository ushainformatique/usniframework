<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\UsniAdaptor;
use yii\base\ViewEvent;
/**
 * Abstract base class for any sub view in the application.
 * 
 * @author Mayank Singhai <mayank.singhai@ushainformatique.com>
 * @package usni\library\views
 */
abstract class UiView extends \yii\base\Component
{
    /**
     * @event Event an event that is triggered before renderContent start preparing the content.
     */
    const EVENT_BEFORE_RENDER_CONTENT = 'beforeRenderContent';
    
    private $_view;

    /**
     * Renders the content.
     * @return void
     */
    abstract protected function renderContent();

    /**
     * Renders view.
     * @return string
     */
    public function render()
    {
        $this->registerAssets();
        $content = null;
        if ($this->beforeRender($content))
        {
            $content .= $this->renderFlashMessages();
            $content .= $this->renderContent();
            $this->afterRender($content);
        }
        return $content;
    }

    /**
     * Gets the id for the view.
     * @return string
     */
    public function getId()
    {
        return get_called_class();
    }

    /**
     * This method is invoked at the beginning of {@link render()}.
     * @param string &$content The content to be rendered.
     * @return boolean whether the view should be rendered.
     */
    protected function beforeRender(&$content)
    {
        $event = new ViewEvent(['output' => $content]);
        $this->trigger(self::EVENT_BEFORE_RENDER_CONTENT, $event);
        $content = $event->output;
        return $event->isValid;
    }

    /**
     * This method is invoked after the specified is rendered by calling {@link render()}.
     * @param string &$output The rendering result of the view. Note that this parameter is passed as a reference.
     * @return null
     */
    protected function afterRender(&$output)
    {
        return null;
    }

    /**
     * Register the scripts.
     */
    protected function registerScripts()
    {

    }

    /**
     * Register the css.
     * @return void
     */
    protected function registerCss()
    {

    }

    /**
     * Register the assets.
     * @return void
     */
    protected function registerAssets()
    {
        $this->registerScripts();
        $this->registerCss();
    }

    /**
     * Renders flash messages.
     * @return string
     */
    protected function renderFlashMessages()
    {
        return null;
    }

    /**
     * Returns the view object that can be used to render views or view files.
     * The [[render()]] and [[renderFile()]] methods will use
     * this view object to implement the actual view rendering.
     * If not set, it will default to the "view" application component.
     * @return \yii\web\View the view object that can be used to render views or view files.
     */
    public function getView()
    {
        if ($this->_view === null)
        {
            $this->_view = UsniAdaptor::app()->getView();
        }

        return $this->_view;
    }

    /**
     * Sets the view object to be used by this view.
     * @param View $view the view object that can be used to render views or view files.
     */
    public function setView($view)
    {
        $this->_view = $view;
    }
}