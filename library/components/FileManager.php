<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\UsniAdaptor;
use usni\library\utils\FileUtil;

/**
 * FileManager class file
 * 
 * @package usni\library\components
 */
class FileManager extends BaseFileManager
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        if($this->uploadPath == null)
        {
            $this->uploadPath = UsniAdaptor::app()->getAssetManager()->fileUploadPath;
        }
    }
    
    /**
     * @inheritdoc
     */
    public static function getType()
    {
        return 'file';
    }
    
    /**
     * Delete the file from uploads folder.
     * @return void
     */
    public function delete()
    {
        $path       = $this->uploadPath;
        $fileName   = $this->model->{$this->attribute};
        $filePath   = FileUtil::normalizePath($path . DS . $fileName);
        if(file_exists($filePath) && is_file($filePath))
        {
            unlink($filePath);
        }
    }
    
    /**
     * Download the file.
     * @param string $fileName
     * @return void
     */
    public function download($fileName)
    {
        $path       = $this->uploadPath;
        $filePath   = FileUtil::normalizePath($path . DS . $fileName);
        if(file_exists($filePath) && is_file($filePath))
        {
            UsniAdaptor::app()->response->sendFile($filePath);
        }
    }
}
