<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */

namespace usni\library\modules\auth\views;

use usni\library\extensions\bootstrap\views\UiBootstrapEditView;
use usni\UsniAdaptor;
use usni\library\components\UiHtml;
use usni\library\modules\auth\utils\AuthDropdownUtil;
use usni\library\utils\ButtonsUtil;
use usni\library\utils\StatusUtil;
use usni\library\modules\users\models\User;
use usni\library\modules\auth\views\GroupBrowseModelView;
/**
 * GroupEditView class file
 *
 * @package usni\library\modules\auth\views
 */
class GroupEditView extends UiBootstrapEditView
{
    /**
     * Member model classes which would be listed in members dropdown
     * @var array 
     */
    public $memberModelClasses;
    
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        if($this->model->getIsNewRecord())
        {
            $this->model->created_by = UsniAdaptor::app()->user->getUserModel()->id;
        }
        $elements = [
                        'name'              => ['type' => 'text'],
                        'parent_id'         => UiHtml::getFormSelectFieldOptions($this->model->getMultiLevelSelectOptions('name', 0, '-', true, $this->shouldRenderOwnerCreatedModels())),
                        'status'            => UiHtml::getFormSelectFieldOptionsWithNoSearch(StatusUtil::getDropdown()),
                        //Fix it and then uncomment it.
                        'members'           => UiHtml::getFormSelectFieldOptions($this->getGroupMembersSelectData(),
                                                                        array('closeOnSelect' => false),
                                                                        array('multiple' => 'multiple'))
                    ];

        $metadata = [
                        'elements'          => $elements,
                        'buttons'           => ButtonsUtil::getDefaultButtonsMetadata('auth/group/manage')
                    ];
        return $metadata;
    }
    
    /**
     * Get group members select data
     * @return array
     */
    protected function getGroupMembersSelectData()
    {
        if($this->memberModelClasses == null)
        {
            $this->memberModelClasses = [User::className()];
        }
        return AuthDropdownUtil::getGroupMembersSelectData($this->memberModelClasses);
    }
    
    /**
     * @inheritdoc
     */
    protected static function resolveBrowseModelViewClassName()
    {
        return GroupBrowseModelView::className();
    }
}
?>
