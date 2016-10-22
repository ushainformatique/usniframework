<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\library\modules\users\models\UserMetadata;
use yii\grid\DataColumn;
use yii\grid\CheckboxColumn;
use usni\UsniAdaptor;
use usni\library\utils\ArrayUtil;
use usni\library\components\UiGridView;

/**
 * MetadataUtil class file.
 *
 * @package usni\library\utils
 */
class MetadataUtil
{
    /**
     * Gets view metadata.
     * @param string $viewClassName
     * @param int $userId
     * @return array
     */
    public static function getUserMetaDataForView($viewClassName, $userId)
    {
        $data           = array();
        $metadataRecord = self::getUserMetaDataRecordForClassName($viewClassName, $userId);
        if($metadataRecord != null)
        {
            $data = unserialize($metadataRecord->serializeddata);
        }
        return $data;
    }

    /**
     * Gets class metadata.
     * @param string $className
     * @param int $userId
     * @return UserMetadata Active Record
     */
    public static function getUserMetaDataRecordForClassName($className, $userId)
    {
        $key            = self::getCacheKey($className, $userId);
        $metadataRecord = CacheUtil::get($key);
        if($metadataRecord === false)
        {
            $activeQuery = UserMetadata::find();
            //Cachekey is usermetadata_userid_classname
            $metadataRecord = $activeQuery
                              ->where('classname = :cn AND user_id = :uid', [':cn'  => $className,
                                                                           ':uid' => $userId])
                              ->one();
            if($metadataRecord != null)
            {
                CacheUtil::set($key, $metadataRecord);
            }
        }
        return $metadataRecord;
    }
    
    /**
     * Get grid columns by display order.
     * @param array $columns
     * @param string $ownerClassName
     * @param array $cells
     * @return array
     */
    public static function getGridColumnsByDisplayOrder($columns, $ownerClassName, $cells)
    {
        $user           = UsniAdaptor::app()->user->getUserModel();
        if($user != null)
        {
            $userId         = UsniAdaptor::app()->user->getUserModel()->id;
            $metadataRecord = self::getUserMetaDataForView($ownerClassName, $userId);
            $isColumnsChanged = false;
            if (empty($metadataRecord))
            {
                $displayedColumns   = $cells;
                $isColumnsChanged   = true;
            }
            else
            {
                $displayedColumns = ArrayUtil::getValue($metadataRecord, 'displayedColumns', []);
                foreach($displayedColumns as $displayColumn)
                {
                    if(in_array($displayColumn, $cells) === false)
                    {
                        $isColumnsChanged = true;
                        $displayedColumns   = $cells;
                        break;
                    }
                }
            }
            if($isColumnsChanged)
            {
                self::saveRecord(new UserMetadata(), $ownerClassName, $userId, ['displayedColumns' => $displayedColumns,
                                                                                'availableColumns' => [],
                                                                                'modalDetailView'  => true,
                                                                                'viewClassName'    => $ownerClassName,
                                                                                'itemsPerPage'     => UiGridView::DEFAULT_LIST_SIZE
                                                                               ]);
            }
            $labelToColMap = [];
            foreach ($columns as $index => $column)
            {
                if ($column instanceof DataColumn)
                {
                    $label = strip_tags($column->renderHeaderCell());
                    if(in_array($label, $displayedColumns) === false)
                    {
                        unset($columns[$index]);
                    }
                    else
                    {
                        $labelToColMap[$label] = $column;
                    }
                }
                elseif($column instanceof CheckboxColumn)
                {
                    $labelToColMap['checkboxcol'] = $column;
                }
                else
                {
                    $labelToColMap['actioncol'] = $column;
                }
            }
            $modifiedColumns = [];
            if(ArrayUtil::getValue($labelToColMap, 'checkboxcol') != null)
            {
                $modifiedColumns[] = $labelToColMap['checkboxcol'];
            }
            foreach($displayedColumns as $inputColumn)
            {
                $modifiedColumns[] = $labelToColMap[$inputColumn];
            }
            if(ArrayUtil::getValue($labelToColMap, 'actioncol') != null)
            {
                $modifiedColumns[] = $labelToColMap['actioncol'];
            }
            return $modifiedColumns;
        }
        return $columns;
    }
    
    /**
     * Clear cache for the metadata record
     * @param string $className
     * @param int $userId
     * @return void
     */
    public static function clearCache($className, $userId)
    {
        $key = self::getCacheKey($className, $userId);
        CacheUtil::delete($key);
    }
    
    /**
     * Get cache key for user metadata.
     * @param string $className
     * @param int $userId
     * @return string
     */
    public static function getCacheKey($className, $userId)
    {
        return 'usermetadata_' . $userId . '_' . strtolower($className);
    }
    
    /**
     * Save metadata record
     * @param UserMetadata $metadataRecord
     * @param string $ownerClassName
     * @param int $userId
     * @param array $unserializedData
     * @return void
     */
    public static function saveRecord($metadataRecord, $ownerClassName, $userId, $unserializedData)
    {
        $metadataRecord->classname      = $ownerClassName;
        $metadataRecord->serializeddata = serialize($unserializedData);
        $metadataRecord->user_id        = $userId;
        $metadataRecord->save(); 
        //Clear cache
        MetadataUtil::clearCache($ownerClassName, $userId);
    }
}
?>
