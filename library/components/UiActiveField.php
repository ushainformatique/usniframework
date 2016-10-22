<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

/**
 * UiActiveField class file.
 * 
 * @package usni\library\components
 */
class UiActiveField extends \yii\bootstrap\ActiveField
{
    /**
     * @inheritdoc
     */
    public $horizontalCheckboxTemplate = "<div class=\"checkbox\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n</div>\n{error}";
}
