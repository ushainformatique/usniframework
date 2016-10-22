<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\widgets;

use usni\library\components\UiHtml;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\Widget;
/**
 * UiLabel renders a label bootstrap component.
 *
 * For example,
 *
 * ```php
 * // a label with configuration
 * echo UiLabel::widget([
 *     'content' => 'Hello World',
 *     'modifier' => 'info'
 * ]);
 * ```
 * @see http://getbootstrap.com/components/#labels
 */
class UiLabel extends Widget
{
    /**
     * Content to be rendered within the label.
     * @var string
     */
    public $content;
    /**
     * @var string modify the label color e.g. info, danger etc.
     */
    public $modifier;

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();
        UiHtml::addCssClass($this->options, 'label');
        if($this->modifier != null)
        {
            UiHtml::addCssClass($this->options, 'label-' . $this->modifier);
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo UiHtml::tag('span', $this->content, $this->options);
        BootstrapAsset::register($this->getView());
    }
}
