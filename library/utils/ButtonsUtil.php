<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\UsniAdaptor;
use usni\library\components\UiHtml;
/**
 * ButtonUtil class file.
 * 
 * @package usni\library\utils
 */
class ButtonsUtil
{
    /**
     * Get metadata for a submit button.
     * @param string $label Label of button.
     * @param string $id id of button.
     * @return array
     */
    public static function getSubmitButton($label, $id = 'save')
    {
        return array(
            'type'      => 'submit',
            'label'     => $label,
            'id'        => $id
        );
    }

    /**
     * Get default buttons metadata.
     * @param string $cancelUrl Cancel Url.
     * @param string $label.
     * @return array
     */
    public static function getDefaultButtonsMetadata($cancelUrl, $label = null)
    {
        if($label == null)
        {
            $label = UsniAdaptor::t('application', 'Save');
        }
        //@see https://github.com/yiisoft/yii2/issues/9351. We can not provide the name as submit
        return array(
            'save'   => self::getSubmitButton($label),
            'cancel' => self::getCancelLinkElementData($cancelUrl)
        );
    }
    
    /**
     * Get link metadata.
     * @param string $label
     * @param string $url
     * @param string $id
     * @return array
     */
    public static function getLinkElementData($label, $url, $id = null)
    {
        $data = array(
            'type'  => 'link',
            'label' => $label,
            'url'   => $url
        );
        if($id != null)
        {
            $data['id'] = $id;
        }
        return $data;
    }

    /**
     * Get default cancel link metadata.
     * @param string $route  route url.
     * @param string $params params.
     * @return array
     */
    public static function getCancelLinkElementData($route, $params = array())
    {
        return array(
            'type'  => 'link',
            'label' => UsniAdaptor::t('application', 'Cancel'),
            'url'   => UsniAdaptor::createUrl($route, $params)
        );
    }

    /**
     * Gets run button.
     * @param string $url
     * @return string
     */
    public static function getRunButton($url)
    {
        return UiHtml::a(UsniAdaptor::t('application', 'Run'), $url, ['class' => 'btn btn-primary']);
    }

    /**
     * Get default search button metadata.
     * @param string $cancelUrl Create Url.
     * @return array
     */
    public static function getDefaultSearchButtonMetadata()
    {
        return array(
            'search'   => self::getSubmitButton(UsniAdaptor::t('application', 'Search'), 'searchformbtn'),
        );
    }

    /**
     * Get default modalbuttons metadata.
     * @param string $cancelUrl Cancel Url.
     * @param string $buttonId.
     * @param string $modalId.
     * @return array
     */
    public static function getDefaultModalButtonsMetadata($cancelUrl, $buttonId = 'savebutton', $modalId)
    {
        return array(
            'save'   => self::getSubmitButton(UsniAdaptor::t('application', 'Save'), $buttonId),
            'cancel' => self::getModalCancelLinkElementData($modalId)
        );
    }

    /**
     * Get default cancel link metadata.
     * @param string $modalId.
     * @return array
     */
    public static function getModalCancelLinkElementData($modalId)
    {
        return array(
            'type'  => 'link',
            'label' => UsniAdaptor::t('application', 'Cancel'),
            'url'   => '#',
            'id'    => "cancel-{$modalId}"
        );
    }

    /**
     * Get default preview link metadata.
     * @param string $route  route url.
     * @param string $params params.
     * @return array
     */
    public static function getPreviewLinkElementData()
    {
        return array(
            'type'       => 'link',
            'name'       => 'preview',
            'label'      => UsniAdaptor::t('application', 'Preview'),
            'id'         => 'preview-button',
            'target'     => '_blank',
            'url'        => '#'   
        );
    }
}
