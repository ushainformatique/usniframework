<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\traits;

use usni\UsniAdaptor;
use yii\db\ActiveQuery;
use usni\library\utils\ArrayUtil;
use usni\library\components\UiHtml;
use usni\library\utils\ConfigurationUtil;

/**
 * Contains utility methods related to tree/multilevel configuration.
 * 
 * @package usni\library\traits
 */
trait TreeModelTrait
{
    /**
     * Get descendants based on a parent.
     * @param int $parentId
     * @param int $isChildren If only childrens have to be fetched
     * @return boolean
     */
    public function descendants($parentId = 0, $isChildren = false)
    {
        $recordsData    = [];
        $className      = $this->getClassName();
        $activeQuery    = new ActiveQuery($className);
        $records        = $activeQuery->where('parent_id = :pid')->params([':pid' => $parentId])->all();
        if(!$isChildren)
        {
            foreach($records as $record)
            {
                $hasChildren    = false;
                $childrens      = $this->descendants($record->id, $isChildren);
                if(count($childrens) > 0)
                {
                    $hasChildren = true;
                }
                $recordsData[]  = ['row'         => $record,
                                   'hasChildren' => $hasChildren, 
                                   'children'    => $childrens];
            }
            return $recordsData;
        }
        else
        {
            return $records;
        }
    }
    
    /**
     * Get childrens.
     * @param int $parentId
     * @return array
     */
    public function childrens($parentId = 0)
    {
        return $this->descendants($parentId, true);
    }

    /**
     * Get the item level.
     * @return integer
     */
    public function getLevel()
    {
        $parentId = $this->getInstance()->parent_id;
        if($parentId == 0 || $parentId == null)
        {
            return 0;
        }
        else
        {
            $modelClassName = $this->getClassName();
            $record = $modelClassName::findOne($parentId);
            return intval($record->level) + 1;
        }
    }
    
    /**
     * Flatten array
     * @param array $items
     * @return array
     */
    public static function flattenArray($items)
    {
        $data = array();
        foreach($items as $index => $item)
        {
            if($item['hasChildren'] == true)
            {
                $children   = $item['children'];
                unset($item['children']);
                $data[]     = $item;
                $data       = ArrayUtil::merge($data, static::flattenArray($children));
            }
            else
            {
                $data[]     = $item;
            }
        }
        return $data;
    }

    /**
     * Get items dropdown in a tree format.
     * @param string  $textFieldName Text field name.
     * @param integer $parentId      Parent  Id.
     * @param string  $delimiter.
     * @param bool $shouldReturnOwnerCreatedModelsOnly
     * @return array
     */
    public function getMultiLevelSelectOptions($textFieldName,
                                               $parentId = 0,
                                               $delimiter = '-',
                                               $isDefaultPrompt = true,
                                               $shouldReturnOwnerCreatedModelsOnly = false,
                                               $valueFieldName = 'id')
    {
        $itemsArray     = [];
        if($isDefaultPrompt)
        {
            $itemsArray = ['' => UiHtml::getDefaultPrompt()];
        }
        $items   = $this->descendants($parentId, false);
        $items   = static::flattenArray($items);
        foreach($items as $item)
        {
            $row = $item['row'];
            if($shouldReturnOwnerCreatedModelsOnly)
            {
                if($this->getInstance()->$valueFieldName != $row->$valueFieldName)
                {
                    //In case of new record, created_by should be set before calling this function @see PageEditView
                    if($this->getInstance()->created_by == $row->created_by)
                    {
                        $itemsArray[$row->$valueFieldName] = str_repeat($delimiter, $row->level) . $row->$textFieldName;
                    }
                }
            }
            else
            {
                if($this->getInstance()->$valueFieldName != $row->$valueFieldName)
                {
                    $itemsArray[$row->$valueFieldName] = str_repeat($delimiter, $row->level) . $row->$textFieldName;
                }
            }
        }
        return $itemsArray;
    }

    /**
     * In case the parent is deleted, set the children parent as null.
     * @param string $tableName
     * @return void
     */
    public function setParentAsNullForChildrenOnDelete($tableName)
    {
        UsniAdaptor::db()->createCommand()->update($tableName,
                                                    ['parent_id' => 0, 'level' => $this->getInstance()->level],
                                                    'parent_id = :pid',
                                                    [':pid' => $this->id])->execute();
    }

    /**
     * Updates children level.
     * @return void
     */
    public function updateChildrensLevel()
    {
        $rows = $this->getInstance()->findAll(['parent_id' => $this->id]);
        foreach($rows as $row)
        {
            $row->level = $this->getInstance()->level + 1;
            $row->save();
            $row->updateChildrensLevel();
        }
    }
    
    /**
     * Get items records in a tree format in hierarchy.
     * @param $parentId integer
     * @return array
     */
    public function getTreeRecordsInHierarchy($parentId = 0)
    {
        $items   = $this->descendants($parentId, false);
        $items   = static::flattenArray($items);
        $rawData = [];
        foreach ($items as $item)
        {
            $rawData[$item['row']['id']] = $item['row'];
        }
        return $rawData;
    }
    
    /**
     * Get parent filter
     * @return array
     */
    public function getParentFilterDropdown()
    {
        $className  = $this->getClassName();
        $translatedClassName = $className . 'Translated';
        $language   = UsniAdaptor::app()->languageManager->getContentLanguage();
        $tableName  = $className::tableName();
        $trTableName = $translatedClassName::tableName();
        $query      = $className::find();
        $results    = $query->select('owner_id, name')
                        ->from([$tableName . ' t1', $tableName . ' t2', $trTableName . ' tgt'])
                        ->where('t1.parent_id = t2.id AND t2.id = tgt.owner_id AND tgt.language = :lang', [':lang' => $language])
                        ->asArray()->all();
        $results    = ArrayUtil::map($results, 'owner_id', 'name');
        $results[0] = UsniAdaptor::t('application', '(not set)');
        return $results;
    }
    
    /**
     * Get level filter
     * @return array
     */
    public function getLevelFilterDropdown()
    {
        $className  = $this->getClassName();
        $results    = $className::find()->select('level')->distinct()->asArray()->all();
        return ArrayUtil::map($results, 'level', 'level');
    }
    
    /**
     * Get parent name.
     * @param ActiveRecord $data
     * @return string
     */
    public static function getParentName($data, $key, $index, $column)
    {
        if($data->parent_id == 0)
        {
            return UsniAdaptor::t('application', '(not set)');
        }
        $record = $data::find()->where('id = :id', [':id' => $data->parent_id])->one();
        if(!empty($record))
        {
            return $record->name;
        }
        return UsniAdaptor::t('application', '(not set)');
    }
    
    /**
     * Get qualified class name. If owner is a behavior, return owner class name.
     * @return string
     */
    protected function getClassName()
    {
        if($this instanceof \yii\base\Behavior)
        {
            return get_class($this->owner);
        }
        return get_class($this);
    }
    
    /**
     * Get qualified class name. If owner is a behavior, return owner class name.
     * @return string
     */
    protected function getInstance()
    {
        if($this instanceof \yii\base\Behavior)
        {
            return $this->owner;
        }
        return $this;
    }
}