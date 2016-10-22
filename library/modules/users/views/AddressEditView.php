<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\components\UiHtml;
use usni\library\utils\CountryUtil;
/**
 * AddressEditView class file.
 *
 * @package usni\library\modules\users\views
 */
class AddressEditView extends \usni\library\views\MultiModelEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $elements = [
                        'address1'      => array('type' => 'text'),
                        'address2'      => array('type' => 'text'),
                        'city'          => array('type' => 'text'),
                        'state'         => array('type' => 'text'),
                        'country'       => UiHtml::getFormSelectFieldOptions(CountryUtil::getCountries()),
                        'postal_code'   => array('type' => 'text'),
                    ];
        $metadata = [
                        'elements'              => $elements
                    ];
        return $metadata;
    }
}