<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

/**
 * NumberUtil class file.
 * 
 * @package usni\library\utils
 */
class NumberUtil
{
    /**
     * Compare float
     * @param float $a
     * @param float $b
     * @return boolean
     */
    public static function compareFloat($a, $b)
    {
        $epsilon = 0.00001;
        if(abs($a - $b) < $epsilon) 
        {
            return true;
        }
        return false;
    }
}