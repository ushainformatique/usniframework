<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\views\UiDetailView;
use usni\library\utils\StatusUtil;
/**
 * View to render user address information.
 * @package usni\library\modules\users\views
 */
class UserAddressView extends UiDetailView
{
    /**
     * Get columns.
     * @return array
     */
    public function getColumns()
    {
        return array(
                        'address1',
                        'address2',
                        'city',
                        'state',
                        [
                           'attribute'  => 'country',
                           'value'      => $this->model->getCountryName() 
                        ],
                        'postal_code',
                        ['attribute' => 'status',   'value' => StatusUtil::renderLabel($this->model), 'format' => 'html'],
                    );
    }

    /**
     * Should title be rendered
     * @return boolean
     */
    protected function shouldRenderTitle()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected static function shouldRenderCreatedAttributes()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected static function shouldRenderModifiedAttributes()
    {
        return false;
    }
    
    /**
     * @inheritdoc
     */
    protected function renderDetailModelBrowseView()
    {
        return null;
    }  
}
?>