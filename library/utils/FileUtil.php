<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;
/**
 * Contains file utility functions.
 * 
 * @package usni\library\utils
 */
class FileUtil extends \yii\helpers\FileHelper
{
    /**
     * Writes file.
     * @param string $path
     * @param string $mode
     * @param string $content
     * @return void
     */
    public static function writeFile($path, $fileName, $mode = 'wb', $content)
    {
        $fileName   = $path . '/' . $fileName;
        $fp         = fopen($fileName, $mode);
        fwrite($fp, $content);
        fclose($fp);
    }
    
    /**
     * Clear assets
     * @return void
     */
    public static function clearAssets()
    {
        $dirs = [APPLICATION_PATH . '/assets', APPLICATION_PATH . '/backend/assets'];
        foreach($dirs as $dir)
        {
            $files = glob($dir . '/*');
            foreach($files as $file)
            { 
                FileUtil::removeDirectory($file);
            }
        }
    }
    
    /**
     * @return string file extension
     */
    public static function getExtension($name)
    {
        return strtolower(pathinfo($name, PATHINFO_EXTENSION));
    }
}