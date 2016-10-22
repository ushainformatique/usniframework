<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\widgets;

use usni\library\components\UiHtml;
/**
 * UiListViewWidget class file
 * @package usni\library\widgets
 */
class UiListViewWidget extends \yii\widgets\ListView
{
    /**
     * @inheritdoc
     */
    public $layout = "{caption}\n<div class='panel panel-content'>{summary}\n{items}\n{pager}</div>";
    
    /**
     * View within which the list view would be rendered
     * @var UiListView
     */
    public $owner;
    
    /**
     * @var string the caption of the list table
     * @see captionOptions
     */
    public $caption;
    /**
     * @var array the HTML attributes for the caption element.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     * @see caption
     */
    public $captionOptions = [];
    
    /**
     * @inheritdoc
     */
    public function renderSection($name)
    {
        switch ($name)
        {
            case "{caption}":
                return $this->renderCaption();
            default:
                return parent::renderSection($name);
        }
    }

    /**
     * Renders caption.
     * @return string
     */
    public function renderCaption()
    {
        if (!empty($this->caption))
        {
            $caption = UiHtml::tag('h3', $this->caption, $this->captionOptions);
            return UiHtml::tag('div', $caption, ['class' => 'page-header']);
        }
        return null;
    }
}
?>