<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

/**
 * VideoManager class file
 * 
 * @package usni\library\components
 */
class VideoManager extends BaseFileManager
{
    /**
     * @inheritdoc
     */
    public static function getType()
    {
        return 'video';
    }
}