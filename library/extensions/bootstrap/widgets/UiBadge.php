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
 * UiBadge renders a badge bootstrap component.
 *
 * For example,
 *
 * ```php
 * // a label with configuration
 * echo UiBadge::widget([
 *     'content' => 'Hello World'
 * ]);
 * ```
 * @see http://getbootstrap.com/components/#badges
 */
class UiBadge extends Widget
{
    /**
     * Content to be rendered within the badge.
     * @var string
     */
    public $content;

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();
        UiHtml::addCssClass($this->options, 'badge');
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
