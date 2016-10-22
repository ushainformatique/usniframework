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
 * UiHeading renders a heading bootstrap component.
 *
 * For example,
 *
 * ```php
 * // a label with configuration
 * echo UiHeading::widget([
 *     'tag' => 'h1',
 *     'content' => 'Hello World'
 * ]);
 * ```
 * @see http://getbootstrap.com/css/#type
 */
class UiHeading extends Widget
{
    /**
     * Content to be rendered within the label.
     * @var string
     */
    public $content;
    /**
     * @var string heading tag, h1, h2, h3
     */
    public $tag;

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo UiHtml::tag($this->tag, $this->content, $this->options);
        BootstrapAsset::register($this->getView());
    }
}
