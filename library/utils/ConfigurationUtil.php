<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\library\models\Configuration;
use usni\library\utils\CacheUtil;
use yii\helpers\Json;
use usni\UsniAdaptor;
use usni\library\modules\users\models\User;
/**
 * ConfigurationUtil class file.
 * 
 * @package usni\library\utils
 */
class ConfigurationUtil
{
    /**
     * Checks if configuration exist.
     * @param string $module
     * @param string $key
     * @return array
     */
    public static function checkAndGetConfigurationIfExist($module, $key)
    {
        $tableName = Configuration::tableName();
        $sql       = "SELECT * FROM $tableName tc WHERE tc.module = :module AND tc.key = :key";
        return UsniAdaptor::app()->db->createCommand($sql, [':module' => $module, ':key' => $key])->queryOne();
    }

    /**
     * Insert or update configuration
     * @param string $module
     * @param string $key
     * @param string $value
     * @return array
     */
    public static function insertOrUpdateConfiguration($module, $key, $value)
    {
        $user   = UsniAdaptor::app()->user->getUserModel();
        if($user == null)
        {
            //Install time
            $createdBy = $modifiedBy = User::SUPER_USER_ID;
        }
        else
        {
            $createdBy = $modifiedBy = $user->id;
        }
        $createdDateTime = $modifiedDateTime = date('Y-m-d H:i:s');
        $record = ConfigurationUtil::checkAndGetConfigurationIfExist($module, $key);
        try
        {
            if($record === false)
            {
                $data   = [
                    'key'    => $key,
                    'value'  => $value,
                    'module' => $module,
                    'created_by' => $createdBy,
                    'created_datetime' => $createdDateTime,
                    'modified_by' => $modifiedBy,
                    'modified_datetime' => $modifiedDateTime
                ];
                UsniAdaptor::app()->db->createCommand()->insert(Configuration::tableName(), $data)->execute();
            }
            else
            {
                $data   = [
                    'value'  => $value,
                    'modified_by' => $modifiedBy,
                    'modified_datetime' => $modifiedDateTime
                ];
                UsniAdaptor::app()->db->createCommand()->update(Configuration::tableName() . ' tc', $data, 
                                                                'tc.module = :module AND tc.key = :key', [':module' => $module, ':key' => $key])->execute();
            }
            CacheUtil::delete('appconfig');
            return null;
        }
        catch (\yii\db\Exception $e)
        {
            return $e->getMessage();
        }
    }

    /**
     * Process insert or update configuration.
     * @param Model $model
     * @param string $module
     * @param array $excludedAttributes
     * @return void
     */
    public static function processInsertOrUpdateConfiguration($model, $module, $excludedAttributes = [])
    {
        $errors = array();
        foreach($model->getAttributes() as $key => $value)
        {
            if($model instanceof \yii\db\ActiveRecord && $model->getPrimaryKey() == $key)
            {
                continue;
            }
            if(in_array($key, $excludedAttributes))
            {
                continue;
            }
            $errors = ConfigurationUtil::insertOrUpdateConfiguration($module, $key, $value);
            if(!empty($errors))
            {
                $model->addErrors(array($key = $errors));
            }
        }
    }

    /**
     * Get value for the configuration.
     * @param string $module
     * @param string $key
     * @return string
     */
    public static function getValue($module, $key)
    {
        $tableName  = Configuration::tableName();
        $configData = CacheUtil::get('appconfig');
        if($configData === false || !isset($configData[$module][$key]))
        {
            $sql    = "SELECT * FROM $tableName tc WHERE tc.module = :module AND tc.key = :key";
            $record = UsniAdaptor::app()->db->createCommand($sql, [':module' => $module, ':key' => $key])->queryOne();
            if($record === false)
            {
                return null;
            }
            return $record['value'];
        }
        else
        {
            return $configData[$module][$key];
        }
    }

    /**
     * Get module configuration.
     * @param string $module
     * @param bollean $cache
     * @return array
     */
    public static function getModuleConfiguration($module, $cache = true)
    {
        $tableName  = Configuration::tableName();
        $configData = CacheUtil::get('appconfig');
        if($configData === false || !isset($configData[$module]))
        {
            $confData       = array();
            $sql            = "SELECT * FROM $tableName WHERE module = :module";
            $records        = UsniAdaptor::app()->db->createCommand($sql, [':module' => $module])->queryAll();
            foreach($records as $record)
            {
                $confData[$record['key']] = $record['value'];
            }
        }
        else
        {
           $confData = $configData[$module];
        }
        return $confData;
    }
    
    /**
     * Is module enabled
     * @param string $id
     * @return bool
     */
    public static function isModuleEnabled($id)
    {
        $moduleMetadata = ConfigurationUtil::getValue('application', 'moduleMetadata');
        $moduleMetadata = Json::decode($moduleMetadata);
        $doesExist      = ArrayUtil::getValue($moduleMetadata, $id, false);
        if(!$doesExist)
        {
            return false;
        }
        $flattenModuleMetadata = self::flattenModuleMetadata($moduleMetadata);
        $value          = $flattenModuleMetadata[$id];
        return $value['status'];
    }
    
    /**
     * Flatten module metdata
     * @param array $moduleMetadata
     * @return array
     */
    public static function flattenModuleMetadata($moduleMetadata)
    {
        $flatMetadata = [];
        foreach($moduleMetadata as $key => $config)
        {
            $flatMetadata[$key] = $config;
            if(isset($config['modules']))
            {
                $flatMetadata = ArrayUtil::merge($flatMetadata, self::flattenModuleMetadata($config['modules']));
            }
        }
        return $flatMetadata;
    }
    
    /**
     * Delete configuration
     * @param string $key
     * @param string $module
     */
    public static function deleteConfiguration($key, $module)
    {
        $tableName = UsniAdaptor::tablePrefix() . 'configuration';
        $sql       = "DELETE tc FROM $tableName AS tc WHERE tc.key = :key AND tc.module = :module";
        UsniAdaptor::db()->createCommand($sql, [':key' => $key, ':module' => $module])->execute();
    }
}