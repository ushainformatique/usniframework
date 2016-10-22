<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\dataproviders;

use yii\data\ActiveDataProvider;
/**
 * TreeActivelDataProvider extends ActiveDataProvider to render records in hierarcial order
 * 
 * @package usni\library\dataproviders
 */
class TreeActiveDataProvider extends ActiveDataProvider
{
    /**
     * Parent id under which all the records would be fetched
     * @var int
     */
    public $parentId = 0;
    
    /**
     * Hierarchy records
     * @var array 
     */
    public $allModels;
    
    /**
     * Compare attribute
     * @var string 
     */
    public $compareAttribute;
    
    /**
     * Compare direction
     * @var string 
     */
    public $compareDirection;
    
    /**
     * Filtered columns
     * @var array 
     */
    public $filteredColumns = [];
    
    /**
     * Filter model
     * @var Model 
     */
    public $filterModel;
    
    /**
     * @inheritdoc
     */
    public function getModels()
    {
        $this->setAllModels();
        $orderedModels      = [];
        $keys               = [];
        $models             = $this->allModels;
        if (($sort = $this->getSort()) !== false) 
        {
            $attributeOrders = $sort->getAttributeOrders();
            if(!empty($attributeOrders))
            {
                foreach($attributeOrders as $attribute => $direction)
                {
                    $this->compareAttribute = $attribute;
                    $this->compareDirection = $direction;
                    uasort($models, [$this, 'cmp']);
                    if(!$sort->enableMultiSort)
                    {
                        break;
                    }
                }
            }
        }
        
        if (($pagination = $this->getPagination()) !== false) 
        {
            $pagination->totalCount = $this->getTotalCount();
            if ($pagination->getPageSize() > 0) 
            {
                $models = array_slice($models, $pagination->getOffset(), $pagination->getLimit(), true);
            }
        }
        foreach($models as $id => $record)
        {
            $orderedModels[] = $record;
            $keys[] = $id;
        }
        $this->setKeys($keys);
        return $orderedModels;
    }
    
    /**
     * Set all models
     * @return void
     */
    public function setAllModels()
    {
        $modelClass         = $this->query->modelClass;
        $primaryModel       = new $modelClass();
        $this->allModels    = $primaryModel->getTreeRecordsInHierarchy($this->parentId);
        $this->applyFilter();
    }
    
    /**
     * @inheritdoc
     */
    protected function prepareTotalCount()
    {
        return count($this->allModels);
    }
    
    /**
     * Compare models
     * @param Model $a
     * @param Model $b
     * @return bool
     */
    public function cmp($a, $b)
    {
        $attribute = $this->compareAttribute;
        if($attribute != null)
        {
            if($this->compareDirection == SORT_ASC)
            {
                return strcmp($a->$attribute, $b->$attribute);
            }
            if($this->compareDirection == SORT_DESC)
            {
                return strcmp($b->$attribute, $a->$attribute);
            }
        }
        else
        {
            return 0;
        }
    }
    
    /**
     * Apply filters on the model set
     * @return void
     */
    public function applyFilter()
    {
        $filteredModels     = [];
        $allModels          = $this->allModels;
        $columnsToBeFilter = $this->filteredColumns;
        foreach ($allModels as $id => $model)
        {
            $isFilter = true;
            if(!empty($columnsToBeFilter))
            {
                foreach ($columnsToBeFilter as $column)
                {
                    //Null doesn't work here
                    if($this->filterModel->$column !== '' 
                        && $this->filterModel->$column !== false
                            &&$this->filterModel->$column !== null)
                    {
                        $isFilter = $isFilter && call_user_func_array([$this, 'filterValue'], [$model, $column, $this->filterModel->$column]);
                    }
                }
            }
            if($isFilter)
            {
                $filteredModels[$id] = $model;
            }
        }
        $this->allModels = $filteredModels;
    }
    
    /**
     * Filter value.
     * @param Model $model
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public function filterValue($model, $key, $value)
    {
        if($model->$key == $value)
        {
            return true;
        }
        elseif(strpos($model->$key, $value) === false)
        {
            return false;
        }
        return true;
    }
}