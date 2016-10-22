<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use yii\db\ActiveQueryInterface;
use usni\library\utils\ArrayUtil;
/**
 * Provides data for the translated active records.
 * @package usni\library\components
 */
class TranslatedActiveDataProvider extends \yii\data\ActiveDataProvider
{
    /**
     * @inheritdoc
     */
    public function setSort($value)
    {
        parent::setSort($value);
        if (($sort = $this->getSort()) !== false && empty($sort->translatedAttributes) && $this->query instanceof ActiveQueryInterface) 
        {
            /* @var $model Model */
            $model = new $this->query->modelClass;
            $translatedAttributes = $model->getTranslatableAttributes();
            if(!empty($translatedAttributes))
            {
                foreach ($translatedAttributes as $attribute) 
                {
                    $sort->attributes[$attribute] = [
                        'asc' => [$attribute => SORT_ASC],
                        'desc' => [$attribute => SORT_DESC],
                        'label' => $model->getAttributeLabel($attribute),
                    ];
                }
            }
        }
        else
        {
            $sort->attributes = ArrayUtil::merge($sort->attributes, $sort->translatedAttributes);
        }
    }
}