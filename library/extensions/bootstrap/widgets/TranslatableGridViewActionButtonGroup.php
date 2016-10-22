<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\widgets;

use usni\library\views\LanguageSelectionView;
use usni\library\utils\ApplicationUtil;
/**
 * TranslatableGridViewActionButtonGroup class file. 
 * 
 * @package usni\library\extensions\bootstrap\widgets
 */
class TranslatableGridViewActionButtonGroup extends UiGridViewActionButtonGroup
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $content = parent::run();
        return $content . ApplicationUtil::getMultilanguageDropDown(LanguageSelectionView::className());
    }
}