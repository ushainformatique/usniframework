<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\UsniAdaptor;
use usni\library\modules\users\models\UserMetadata;
use yii\helpers\Json;
use usni\library\models\Configuration;
/**
 * CacheUtil class file.
 * 
 * @package usni\library\utils
 */
class CacheUtil
{
    /**
     * Check if can connect to database or not.
     * @param string $host
     * @param string $dbUsername
     * @param string $dbPassword
     * @param string $$dbPort
     * @throws NotSupportedException
     * @return mixed $error
     */
    public static function checkMemcacheConnection($host, $port)
    {
        $errorNumber    = 0;
        $errorString    = '';
        $timeout        = 2;
        $connection     = @fsockopen($host, $port, $errorNumber, $errorString, $timeout);
        if ($connection !== false)
        {
            fclose($connection);
            return true;
        }
        return array($errorNumber, $errorString);
    }

    /**
     * Clears the cache.
     * @return void.
     */
    public static function clearCache()
    {
        UsniAdaptor::app()->cache->flush();
        UserMetadata::deleteAll();
    }

    /**
     * Sets the cache.
     * @param mixed $key a key identifying the value to be cached. This can be a simple string or
     * a complex data structure consisting of factors representing the key.
     * @param mixed $value the value to be cached
     * @param integer $duration the number of seconds in which the cached value will expire. 0 means never expire.
     * @param Dependency $dependency dependency of the cached item. If the dependency changes,
     * the corresponding value in the cache will be invalidated when it is fetched via [[get()]].
     */
    public static function set($key, $value, $duration = 0, $dependency = null)
    {
        $key = UsniAdaptor::app()->language . '_' . $key;
        UsniAdaptor::app()->cache->set($key, $value, $duration, $dependency);
    }

    /**
     * Retrieves a value from cache with a specified key.
     * @param mixed $key a key identifying the cached value. This can be a simple string or
     * a complex data structure consisting of factors representing the key.
     * @return mixed the value stored in cache, false if the value is not in the cache, expired,
     * or the dependency associated with the cached data has changed.
     */
    public static function get($key)
    {
        $key = UsniAdaptor::app()->language . '_' . $key;
        return UsniAdaptor::app()->cache->get($key);
    }
    
    /**
     * Delete a value from cache with a specified key.
     * @param mixed $key a key identifying the cached value. This can be a simple string or
     * a complex data structure consisting of factors representing the key.
     */
    public static function delete($key)
    {
        $key = UsniAdaptor::app()->language . '_' . $key;
        UsniAdaptor::app()->cache->delete($key);
    }
    
    /**
     * Sets model cache.
     * @param string $modelClassName
     * @param string $key
     * @see TranslationTrait::loadTranslation
     * @return void
     */
    public static function setModelCache($modelClassName, $key)
    {
        $modelCacheKey = UsniAdaptor::getObjectClassName($modelClassName) . 'Cache';
        $value = static::get($modelCacheKey);
        if($value === false)
        {
            static::set($modelCacheKey, Json::encode([$key]));
        }
        else
        {
            $data = Json::decode($value);
            if(!in_array($key, $data))
            {
                $data[] = $key;
                static::set($modelCacheKey, Json::encode($data));
            }
        }
    }
    
    /**
     * Loads configuration.
     * 
     * @return void
     */
    public static function loadConfiguration()
    {
        $configData = [];
        if(UsniAdaptor::app()->isInstalled())
        {
            $configData = self::get('appconfig');
            if($configData === false)
            {
                $configData = [];
                $records    = Configuration::find()->asArray()->all();
                if(!empty($records))
                {
                    foreach($records as $record)
                    {
                        $configData[$record['module']][$record['key']] = $record['value'];
                    }
                }
                self::set('appconfig', $configData);
            }
        }
    }
}
