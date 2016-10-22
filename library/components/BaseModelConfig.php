<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use yii\base\Model;

/**
 * BaseModelConfig class file.
 * 
 * @author Mayank Singhai <mayank.singhai@ushainformatique.com>
 * @package usni\library\components
 */
class BaseModelConfig extends \yii\base\Component
{
    /**
     * Parent class from which config class is called
     * @var string 
     */
    public $parentClass;

    /**
     * Class constructor
     */
    public function __construct($parentClass)
    {
        $this->parentClass = $parentClass;
    }

    /**
     * @inheritdoc
     * 
     * @see yii\base\Model::scenarios()
     */
    public function scenarios()
    {
        $scenarios = [Model::SCENARIO_DEFAULT => []];
        foreach ($this->parentClass->getValidators() as $validator)
        {
            foreach ($validator->on as $scenario)
            {
                $scenarios[$scenario] = [];
            }
            foreach ($validator->except as $scenario)
            {
                $scenarios[$scenario] = [];
            }
        }
        $names = array_keys($scenarios);

        foreach ($this->parentClass->getValidators() as $validator)
        {
            if (empty($validator->on) && empty($validator->except))
            {
                foreach ($names as $name)
                {
                    foreach ($validator->attributes as $attribute)
                    {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            }
            elseif (empty($validator->on))
            {
                foreach ($names as $name)
                {
                    if (!in_array($name, $validator->except, true))
                    {
                        foreach ($validator->attributes as $attribute)
                        {
                            $scenarios[$name][$attribute] = true;
                        }
                    }
                }
            }
            else
            {
                foreach ($validator->on as $name)
                {
                    foreach ($validator->attributes as $attribute)
                    {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            }
        }

        foreach ($scenarios as $scenario => $attributes)
        {
            if (!empty($attributes))
            {
                $scenarios[$scenario] = array_keys($attributes);
            }
        }
        return $scenarios;
    }
}