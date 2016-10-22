<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\UsniAdaptor;
use usni\library\extensions\bootstrap\widgets\UiLabel;
use usni\library\components\UiHtml;

/**
 * StatusUtil class file.
 * 
 * @package usni\library\utils
 */
class StatusUtil
{
    /**
     * Active status constant.
     */
    const STATUS_ACTIVE = 1;
    /**
     * Inactive status constant.
     */
    const STATUS_INACTIVE = 0;
    /**
     * Pending status constant.
     */
    const STATUS_PENDING    = 2;

    /**
     * Gets label for the status.
     * @param string $data ActiveRecord of the model.
     * @return string
     */
    public static function getLabel($data)
    {
        if(is_array($data))
        {
            $data = (object)$data;
        }
        if ($data->status == self::STATUS_ACTIVE)
        {
            return UsniAdaptor::t('application', 'Active');
        }
        else if ($data->status == self::STATUS_INACTIVE)
        {
            return UsniAdaptor::t('application', 'Inactive');
        }
        else if ($data->status == self::STATUS_PENDING)
        {
            return UsniAdaptor::t('application', 'Pending');
        }
    }

    /**
     * Gets status dropdown.
     * @return array
     */
    public static function getDropdown()
    {
        return array(
            self::STATUS_ACTIVE     => UsniAdaptor::t('application', 'Active'),
            self::STATUS_INACTIVE   => UsniAdaptor::t('application', 'Inactive')
        );
    }

    /**
     * Renders label for the status.
     * @param string $data ActiveRecord of the model.
     * @return string
     */
    public static function renderLabel($data)
    {
        if(is_array($data))
        {
            $dataObject = (object)$data;
            $value      = StatusUtil::getLabel($dataObject);
            $className  = UsniAdaptor::getObjectClassName($dataObject);
            $id         = strtolower($className) . '-status-' . $dataObject->id;
        }
        else
        {
            $value      = StatusUtil::getLabel($data);
            $className  = UsniAdaptor::getObjectClassName($data);
            $id         = strtolower($className) . '-status-' . $data->id;
        }
        if ($value == UsniAdaptor::t('application', 'Active'))
        {
            return UiLabel::widget(['content' => $value, 'modifier' => UiHtml::COLOR_SUCCESS, 'id' => $id]);
        }
        elseif ($value == UsniAdaptor::t('application','Inactive'))
        {
            return UiLabel::widget(['content' => $value, 'modifier' => UiHtml::COLOR_WARNING, 'id' => $id]);
        }
        elseif ($value == UsniAdaptor::t('application', 'Pending'))
        {
            return UiLabel::widget(['content' => $value, 'modifier' => UiHtml::COLOR_DANGER, 'id' => $id]);
        }
    }
}