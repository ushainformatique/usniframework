<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;
/**
 * Overrides to handle translated active records.
 * 
 * @package usni\library\components
 */
class Sort extends \yii\data\Sort
{
    /**
     * Translated attributes for the sort
     * @var array 
     */
    public $translatedAttributes = [];
}
