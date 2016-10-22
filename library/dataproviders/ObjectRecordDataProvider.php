<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\dataproviders;

/**
 * ObjectRecordDataProvider is implemented on the same approach as ActiveRecordDataProvider
 * so that we can get array records typecast as stdClass based on limit and not all models in one go
 * as there in ArrayDataProvider.
 * @package usni\library\dataproviders
 */
class ObjectRecordDataProvider extends \yii\data\ActiveDataProvider
{
    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        if($this->query instanceof \yii\db\ActiveQueryInterface)
        {
            $this->query->asArray();
        }
        return parent::prepareModels();
    }
    
    /**
     * @inheritdoc
     */
    public function getModels()
    {
        $models = parent::getModels();
        foreach($models as $index => $model)
        {
            if(is_array($model))
            {
                $models[$index] = (object)$model;
            }
        }
        return $models;
    }
}
?>