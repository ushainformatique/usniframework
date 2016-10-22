<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\UsniAdaptor;
use usni\library\utils\FileUtil;
use usni\library\utils\StringUtil;
/**
 * UiAssetManager class file.
 * 
 * @package usni\library\components
 */
class UiAssetManager extends \yii\web\AssetManager
{
    /**
     * Path for the resources
     * @var string 
     */
    public $resourcesPath;
    /**
     * File upload path for the application.
     * @var string
     */
    public $fileUploadPath;
    /**
     * Image upload path for the application.
     * @var string
     */
    public $imageUploadPath;
    /**
     * Thumbnail upload path for the application.
     * @var string
     */
    public $thumbUploadPath;
    /**
     * Video upload path for the application.
     * @var string
     */
    public $videoUploadPath;
    /**
     * Video thumbnail upload path for the application.
     * @var string
     */
    public $videoThumbUploadPath;
    
    /**
     * Image manager class
     * @var string 
     */
    public $imageManagerClass;
    
    /**
     * File manager class
     * @var string 
     */
    public $fileManagerClass;
    
    /**
     * Video manager class
     * @var string 
     */
    public $videoManagerClass;

    /**
     * Override so that asset directory is created.
     */
    public function init()
    {
        $basePath = UsniAdaptor::getAlias($this->basePath);
        FileUtil::createDirectory($basePath);
        FileUtil::createDirectory($this->resourcesPath);
        FileUtil::createDirectory($this->fileUploadPath);
        FileUtil::createDirectory($this->imageUploadPath);
        FileUtil::createDirectory($this->thumbUploadPath);
        FileUtil::createDirectory($this->videoUploadPath);
        FileUtil::createDirectory($this->videoThumbUploadPath);
        parent::init();
    }

    /**
     * Gets file upload url.
     * @return string
     */
    public function getFileUploadUrl()
    {
        $frontUrl = UsniAdaptor::app()->getFrontUrl();
        $route    = str_replace(APPLICATION_PATH, '', $this->fileUploadPath);
        return StringUtil::replaceBackSlashByForwardSlash($frontUrl . $route);
    }

    /**
     * Gets image upload url.
     * @return string
     */
    public function getImageUploadUrl()
    {
        $frontUrl = UsniAdaptor::app()->getFrontUrl();
        $route    = str_replace(APPLICATION_PATH, '', $this->imageUploadPath);
        return StringUtil::replaceBackSlashByForwardSlash($frontUrl . $route);
    }

    /**
     * Gets thumbnail upload url.
     * @return string
     */
    public function getThumbnailUploadUrl()
    {
        $frontUrl = UsniAdaptor::app()->getFrontUrl();
        $route    = str_replace(APPLICATION_PATH, '', $this->thumbUploadPath);
        return StringUtil::replaceBackSlashByForwardSlash($frontUrl . $route);
    }
    
    /**
     * Get resource manager instance
     * @param string $type
     * @param array $config
     * @return \usni\library\components\BaseFileManager
     */
    public function getResourceManagerInstance($type, $config = [])
    {
        if($type == 'image')
        {
            $imageManagerClass = $this->imageManagerClass;
            return new $imageManagerClass($config);
        }
        elseif($type == 'file')
        {
            $fileManagerClass = $this->fileManagerClass;
            return new $fileManagerClass($config);
        }
        elseif($type == 'video')
        {
            $videoManagerClass = $this->videoManagerClass;
            return new $videoManagerClass($config);
        }
    }
}