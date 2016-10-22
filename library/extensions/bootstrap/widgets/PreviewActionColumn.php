<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\widgets;

use usni\library\components\UiHtml;
use usni\fontawesome\FA;
use usni\UsniAdaptor;

/**
 * PreviewActionColumn class file.
 * @package usni\library\extensions\bootstrap\widgets
 */
/**
 * Bootstrap action column widget.
 */
class PreviewActionColumn extends UiActionColumn
{
    /**
     * Initializes the default button rendering callbacks
     */
    protected function initDefaultButtons()
    {
        parent::initDefaultButtons();
        if (!isset($this->buttons['preview']))
        {
            $this->buttons['preview'] = array($this, 'renderPreviewActionLink');;
        }
    }

    /**
     * Renders preview action link.
     * @param string $url
     * @param Model $model
     * @param string $key
     * @return string
     */
    protected function renderPreviewActionLink($url, $model, $key)
    {
        if($this->checkAccess($model, 'view'))
        {
            $label = UsniAdaptor::t('application', 'Preview');
            $icon  = "\n" . FA::icon('eye-slash');
            return UiHtml::a($icon, '#', [
                                                'title'         => $label,
                                                'id'            => 'preview-button-' . $model->id, 
                                                'data-alias'    => $model->alias,
                                                'class'         => 'grid-preview-link'
                                          ]);
        }
        return null;
    }
}