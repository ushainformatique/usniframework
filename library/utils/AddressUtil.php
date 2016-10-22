<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl.html
 */
namespace usni\library\utils;

use usni\library\modules\users\models\Address;
/**
 * AddressUtil class file
 * @package common\utils
 */
class AddressUtil
{
    /**
     * Get address type to prefix mapping.
     * @return array
     */
    public static function getAddressTypeToPrefixMapping()
    {
        return ['ShippingAddress' => 'shipping_', 
                'BillingAddress' => 'billing_',
                'DefaultAddress' => 'default_'];
    }
    
    /**
     * Get ignored attributes.
     * @return array
     */
    public static function getIgnoredAttributes()
    {
        return ['type', 'status', 'relatedmodel', 'relatedmodel_id'];
    }    
    
    /**
     * Save address
     * @param Address $address
     * @param Model $formModel Input form model
     * @param Array $addressAttributes
     * @return boolean
     */
    public static function saveAddress($address, $formModel, $addressAttributes, $addressType = null)
    {
        $attributePrefix = '';
        $mapping         = self::getAddressTypeToPrefixMapping();
        if($addressType != null)
        {
            $attributePrefix = $mapping[$addressType];
        }
        foreach($addressAttributes as $attribute)
        {
            $addressAttribute = str_replace($attributePrefix, '', $attribute);
            if($formModel->scenario == 'bulkedit')
            {
                if(property_exists($formModel, $attribute) && $formModel->$attribute != '')
                {
                    $address->$attribute = $formModel->$attribute;
                }
            }
           else
           {
               if(property_exists($formModel, $attribute) && $formModel->$attribute != '')
               {
                   if(in_array($addressAttribute, self::getIgnoredAttributes()))
                   {
                       continue;
                   }
                   $address->$addressAttribute = $formModel->$attribute;
               }
           }
        }
        if($address->save())
        {
            return true;
        }
        else
        {
            $formModel->errors = $address->errors;
        }
        return false;
    }
    
    /**
     * Get form fields to populate shipping address.
     * @param Model $modelClass
     * @return array
     */
    public static function getFormFieldsToPopulateShippingAddress($modelClass)
    {
        $modelToFormFieldMapping = ['country', 'postal_code', 'state', 'address1', 'address2', 'city'];
        $data                    = [];
        foreach($modelToFormFieldMapping as $attribute)
        {
            $data['shipping_' . $attribute] = $_POST[$modelClass]['billing_' . $attribute];
        }
        return $data;
    }
}