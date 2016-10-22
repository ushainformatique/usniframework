<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\views;

use usni\library\extensions\bootstrap\views\UiBootstrapEditView;
use usni\library\components\UiHtml;
use usni\library\utils\ButtonsUtil;
use usni\library\utils\StatusUtil;
/**
 * RoleEditView class file.
 * @package usni\library\modules\auth\views
 */
class RoleEditView extends UiBootstrapEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
         $elements = [
                          'name'              => ['type' => 'text'],
                          'parent_id'         => UiHtml::getFormSelectFieldOptions($this->model->getMultiLevelSelectOptions('name', 0, '-', true, $this->shouldRenderOwnerCreatedModels())),
                          'status'            => UiHtml::getFormSelectFieldOptionsWithNoSearch(StatusUtil::getDropdown()),
                     ];

        $metadata = [
                          'elements'          => $elements,
                          'buttons'           => ButtonsUtil::getDefaultButtonsMetadata('auth/role/manage')
                    ];
        return $metadata;
    }
}
