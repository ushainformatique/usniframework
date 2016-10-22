<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\library\extensions\bootstrap\widgets\TranslatableGridViewActionToolBar;

/**
 * Base class for rendering translatable grid view.
 * 
 * @package usni\library\components
 */
class TranslatableGridView extends UiGridView
{
    /**
     * Get gridview action toolbar class name.
     * @return string
     */
    public static function getGridViewActionToolBarClassName()
    {
        return TranslatableGridViewActionToolBar::className();
    }
}