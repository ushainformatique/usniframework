<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\web;

use yii\web\AssetBundle;

/**
 * Extends base asset bundle for enhanced functionality.
 * @see Advanced template provided by http://yiiframework.com for Yii2
 */
class UiAssetBundle extends AssetBundle
{
    /**
     * Js files that has to be excluded.
     * @var array 
     */
    public $excludedJs = [];
    
    /**
     * Css files that has to be excluded.
     * @var array 
     */
    public $excludedCss = [];
    
    /**
     * Registers the CSS and JS files with the given view.
     * @param \yii\web\View $view the view that the asset files are to be registered with.
     */
    public function registerAssetFiles($view)
    {
        $manager = $view->getAssetManager();
        foreach ($this->js as $js)
        {
            if(!in_array($js, $this->excludedJs))
            {
                $view->registerJsFile($manager->getAssetUrl($this, $js), $this->jsOptions);
            }
        }
        foreach ($this->css as $css)
        {
            if(!in_array($css, $this->excludedCss))
            {
                $view->registerCssFile($manager->getAssetUrl($this, $css), $this->cssOptions);
            }
        }
    }

}

