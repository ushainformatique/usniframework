<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\validators;

use yii\validators\FileValidator;
/**
 * UiFileSizeValidator class file
 * 
 * @package usni\library\validators
 */
class UiFileSizeValidator extends FileValidator
{
    /**
     * @inheritdoc
    */
    public function init()
    {
        parent::init();
        $this->maxSize  = $this->getSizeLimit();
    }
}
