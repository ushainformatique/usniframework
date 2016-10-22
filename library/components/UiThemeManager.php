<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

/**
 * UiThemeManager class file.
 * 
 * @package usni\library\components
 */
class UiThemeManager extends \yii\base\Component
{
    /**
	 * @return array list of available theme names
	 */
	public function getThemeNames()
	{
		$dirIterator = new \DirectoryIterator(APPLICATION_PATH . '/themes');
        $data        = [];
        foreach ($dirIterator as $info)
        {
            $file = $info->getFilename();
            if($info->isDot())
            {
                continue;
            }
            if($info->isDir() && $file !== '.svn' && $file !== '.hg')
            {
                $data[$file] = $file;
            }
        }
        return $data;
	}
}