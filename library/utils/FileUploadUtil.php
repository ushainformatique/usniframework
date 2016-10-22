<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\UsniAdaptor;
use usni\library\utils\ArrayUtil;
/**
 * Utility functions related to file upload.
 * 
 * @package usni\library\utils
 */
class FileUploadUtil
{   
    /**
     * Saves file.
     * @param string $type.
     * @param array $config.
     * @return void
     */
    public static function save($type, $config = [])
    {
        $fileManagerInstance   = UsniAdaptor::app()->assetManager->getResourceManagerInstance($type, $config);
        $fileManagerInstance->save();
    }
    
    /**
     * Get encrypted file name during file upload.
     * @param string $filename File name.
     * @return string
     */
    public static function getEncryptedFileName($filename)
    {
        return StringUtil::getRandomString(10) . $filename;
    }
    
    /**
     * Get no available image
     * @param array $htmlOptions
     * @return string
     */
    public static function getNoAvailableImage($htmlOptions = [])
    {
        $fileManagerInstance   = UsniAdaptor::app()->assetManager->getResourceManagerInstance('image', $htmlOptions);
        return $fileManagerInstance->getNoAvailableImage(); 
    }
    
    /**
     * Gets thumbnail image.
     * @param Model $model.
     * @param string $attribute Image attribute.
     * @param array $htmlOptions. It could contain width and height of the required image
     * @return mixed
     */
    public static function getThumbnailImage($model, $attribute, $htmlOptions = [])
    {
        $config = ArrayUtil::merge(['model' => $model, 'attribute' => $attribute], $htmlOptions);
        $fileManagerInstance   = UsniAdaptor::app()->assetManager->getResourceManagerInstance('image', $config);
        return $fileManagerInstance->getThumbnailImage();
    }
    
    /**
     * Save custom image.
     * @param Model $model
     * @param string $attribute
     * @param int $width
     * @param int $height
     * @param $targetPath string
     * @param $sourcePath string
     * @return void
     */
    public static function saveCustomImage($model, $attribute, $width, $height)
    {
        $config = ['model' => $model, 'attribute' => $attribute,
                   'thumbWidth' => $width, 'thumbHeight' => $height];
        $imageManager   = UsniAdaptor::app()->assetManager->getResourceManagerInstance('image', $config);
        $imageManager->saveSizedImage();
    }
    
    /**
     * Delete image.
     * @param \usni\library\utils\Model $model
     * @param string $attribute
     * @param int $width
     * @param int $height
     * @param bool $createThumbnail
     */
    public static function deleteImage($model, $attribute, $width, $height, $createThumbnail = true)
    {
        $config = ['model' => $model, 'attribute' => $attribute,
                   'thumbWidth' => $width, 'thumbHeight' => $height, 'createThumbnail' => $createThumbnail];
        $imageManager = UsniAdaptor::app()->assetManager->getResourceManagerInstance('image', $config);
        $imageManager->delete();
    }
    
    /**
     * Check if file exists
     * @param string $path
     * @param string $fileName
     * @param string $prefix
     * @return boolean
     */
    public static function checkIfFileExists($path, $fileName, $prefix = null)
    {
        $filePath = StringUtil::replaceBackSlashByForwardSlash($path . DS . $prefix . $fileName);
        if(file_exists($filePath) && is_file($filePath))
        {
            return true;
        }
        return false;
    }
    
    /**
     * Check for ico image.
     * @param string $file
     * @return boolean
     */
    public static function checkIcoImage($file)
    {
        if(in_array(end(explode('.', $file)), ['ico']))
        {
            return true;
        }
        return false;
    }
    
    /**
     * Get uploaded file path.
     * @param string $type.
     * @param array $config.
     * @return void
     */
    public static function getUploadedFilePath($type, $config = [])
    {
        $fileManagerInstance   = UsniAdaptor::app()->assetManager->getResourceManagerInstance($type, $config);
        return $fileManagerInstance->getUploadedFilePath();
    }
    
    /**
     * Delete file.
     * @param \usni\library\utils\Model $model
     * @param string $attribute
     */
    public static function deleteFile($model, $attribute)
    {
        $config = ['model' => $model, 'attribute' => $attribute];
        $fileManager   = UsniAdaptor::app()->fileManager;
        $fileManager   = \Yii::configure($fileManager, $config);
        $fileManager->delete();
    }
}