<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\UsniAdaptor;
use yii\base\Component;
use usni\library\utils\FileUtil;
use usni\library\components\TranslatableActiveRecord;
use usni\library\utils\ArrayUtil;
use usni\library\components\LanguageManager;
/**
 * UiDataManager is the abstract class for data management in the application.
 * 
 * @package usni\library\components
 */
abstract class UiDataManager extends Component
{
    /**
     * Loads default data.
     * @return boolean
     */
    public static function loadDefaultData()
    {
        static::loadDefaultDependentData();
        return static::processAndInsertData('installdefaultdata.bin', 'default');
    }
    
    /**
     * Loads default data.
     * @return boolean
     */
    public static function loadDemoData()
    {
        static::loadDemoDependentData();
        return static::processAndInsertData('installdemodata.bin', 'demo');
    }
    
    /**
     * Process and insert data.
     * @param string $cacheFile
     * @param string $type
     * @return void
     */
    protected static function processAndInsertData($cacheFile, $type)
    {
        $installedData  = static::getUnserializedData($cacheFile);
        $path           = UsniAdaptor::app()->getRuntimePath();
        $isDataLoaded   = static::checkIfClassDataLoaded($installedData);
        if($isDataLoaded)
        {
            return false;
        }
        $dataSet = static::getDataSetByType($type);
        static::processDataSetAndSaveModel($dataSet, $path, $cacheFile, $installedData, $type);
        return true;
    }
    
    /**
     * Get data set by type.
     * @param string $type
     * @return array
     * @throws \yii\base\NotSupportedException
     */
    protected static function getDataSetByType($type)
    {
        if($type == 'default')
        {
            $dataSet = static::getDefaultDataSet();
        }
        elseif($type == 'demo')
        {
            $dataSet = static::getDefaultDemoDataSet();
        }
        else
        {
            throw new \yii\base\NotSupportedException();
        }
        return $dataSet;
    }

    /**
     * Process data set and save model
     * @param array $dataSet
     * @param string $path
     * @param string $fileName
     * @param array $installedData
     * @param string $type
     */
    protected static function processDataSetAndSaveModel($dataSet, $path, $fileName, $installedData, $type)
    {
        if(!empty($dataSet))
        {
            foreach($dataSet as $index => $set)
            {
                $modelClassName = static::getModelClassName();
                $model          = new $modelClassName(['scenario' => 'create']);
                foreach($set as $attribute => $value)
                {
                    $model->$attribute = $value;
                }
                if(!$model->save())
                {
                    throw new \usni\library\exceptions\FailedToSaveModelException(get_class($model));
                }
                else
                {
                    if(is_subclass_of($modelClassName, TranslatableActiveRecord::className()))
                    {
                        $installTargetLanguage  = UsniAdaptor::app()->language;
                        $translationModel       = $model->getTranslation();
                        $class                  = get_class($translationModel);
                        $translatedLanguages    = LanguageManager::getTranslatedLanguages();
                        foreach($translatedLanguages as $translatedLanguage)
                        {
                            UsniAdaptor::app()->language = $translatedLanguage;
                            $transDataSet   = static::getDataSetByType($type);
                            $currentDataSet = $transDataSet[$index];
                            $trInstance     = new $class;
                            foreach($model->translationAttributes as $attribute)
                            {
                                if(ArrayUtil::getValue($currentDataSet, $attribute) != null)
                                {
                                    $trInstance->$attribute = $currentDataSet[$attribute];
                                }
                            }
                            $trInstance->language = $translatedLanguage;
                            $trInstance->owner_id = $model->id;
                            $trInstance->save();
                        }
                        UsniAdaptor::app()->language = $installTargetLanguage;
                    }
                }
            }
            $installedData[]    = static::getKey();
            FileUtil::writeFile($path, $fileName, 'wb', serialize($installedData));
        }
    }


    /**
     * Get model class name
     * @return string
     */
    public static function getModelClassName()
    {
        throw new \usni\library\exceptions\MethodNotImplementedException(__METHOD__, get_called_class());
    }
    
    /**
     * Get default data set.
     * @return array
     */
    public static function getDefaultDataSet()
    {
        throw new \usni\library\exceptions\MethodNotImplementedException(__METHOD__, get_called_class());
    }
    
    /**
     * Get default language.
     * @return string
     */
    protected static function getDefaultLanguage()
    {
        return UsniAdaptor::app()->language;
    }
    
     /**
     * Get default data set.
     * @return array
     */
    public static function getDefaultDemoDataSet()
    {
        throw new \usni\library\exceptions\MethodNotImplementedException(__METHOD__, get_called_class());
    }
    
    /**
     * Loads default dependent data
     */
    protected static function loadDefaultDependentData()
    {
        
    }
    
    /**
     * Loads demo dependent data
     */
    protected static function loadDemoDependentData()
    {
        
    }
    
    /**
     * Checks if class data loaded.
     * @param array $installedData
     * @param string $className
     * @return boolean
     */
    protected static function checkIfClassDataLoaded($installedData)
    {
        $key            = static::getKey();
        if(in_array($key, $installedData))
        {
            return true;
        }
        return false;
    }
    
    /**
     * Get unserialized data.
     * @param string $fileName
     * @return array
     */
    public static function getUnserializedData($fileName)
    {
        $path           = UsniAdaptor::app()->getRuntimePath();
        if(file_exists($path . '/' . $fileName))
        {
            return unserialize(file_get_contents($path . '/' . $fileName));
        }
        return [];
    }
    
    /**
     * Get key
     * @return string
     */
    public static function getKey()
    {
        $modelClassName = static::getModelClassName();
        if($modelClassName != null)
        {
            $tableName  = $modelClassName::tableName();
            $className  = get_called_class();
            $key        = $tableName . '-' . $className;
        }
        else
        {
            $key        = get_called_class();
        }
        return $key;
    }
    
    /**
     * Write file in case of overridden loadDefaultData and loadDemoData method
     * @param string $file
     */
    public static function writeFileInCaseOfOverRiddenMethod($file)
    {
        $installedData      = static::getUnserializedData($file);
        $installedData[]    = static::getKey();
        $path               = UsniAdaptor::app()->getRuntimePath();
        FileUtil::writeFile($path, $file, 'wb', serialize($installedData));
    }
}