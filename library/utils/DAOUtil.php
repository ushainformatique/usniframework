<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\library\utils\ArrayUtil;
use usni\library\utils\CacheUtil;
/**
 * DAOUtil class file.
 * 
 * @package usni\library\utils
 */
class DAOUtil
{
    /**
     * Gets all rows by parent id.
     * @param int $parentId
     * @param string $modelClassName
     * @return array
     */
    public static function getAllRowsByParentId($parentId, $modelClassName)
    {
        return $modelClassName::find()->where('parent_id = :id', [':id' => $parentId])->all();
    }

    /**
     * Get childrens in case of tree model.
     * @param integer $parentId.
     * @param string $model Table model.
     * @return string
     */
    public static function getAllChildrens($parentId = 0, $model)
    {
        $modelClassName = get_class($model);
        $recordsArray   = [];
        $records        = self::getAllRowsByParentId($parentId, $modelClassName);
        foreach($records as $record)
        {
            $recordsArray[]    = $record;
            $childRecordsArray = self::getAllChildrens($record->id, $model);
            if(!empty($childRecordsArray))
            {
                $recordsArray  = ArrayUtil::merge($recordsArray, $childRecordsArray);
            }
        }
        return $recordsArray;
    }

    /**
     * Gets dropdown field select data.
     * @param string $modelClass
     * @return array
     */
    public static function getDropdownDataBasedOnModel($modelClass)
    {
        $key    = $modelClass . 'DropdownList';
        $data   = CacheUtil::get($key);
        if($data === false)
        {
            $data = ArrayUtil::map($modelClass::find()->indexBy('name')->all(), 'id', 'name');
            CacheUtil::set($key, $data);
            CacheUtil::setModelCache($modelClass, $key);
        }
        return $data;
    }
}