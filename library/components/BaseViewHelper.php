<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

/**
 * BaseViewHelper class file. It would act as base class for the extended view helpers
 * at application level or module level
 * 
 * @package usni\library\components
 */
class BaseViewHelper extends \yii\base\Component
{
    /**
     * Get instance of view.
     * @param string $attribute
     * @param array $params
     * @return object
     */
    public function getInstance($attribute, $params = array())
    {
        $view = $this->$attribute;
        if(is_null($view))
        {
            return null;
        }
        if(is_object($view))
        {
            return $view;
        }
        if(!empty($params))
        {
            return new $view($params);
        }
        else
        {
            return new $view();
        }
    }
}