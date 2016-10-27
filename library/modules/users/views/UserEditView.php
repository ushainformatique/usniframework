<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\components\UiHtml;
use usni\library\utils\TimezoneUtil;
use usni\library\components\UiActiveForm;
use usni\library\modules\auth\models\Group;
use usni\library\modules\users\models\User;
use usni\UsniAdaptor;
/**
 * UserEditView class file.
 * 
 * @package usni\library\modules\users\views
 */
class UserEditView extends \usni\library\views\MultiModelEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $group              = new Group();
        $group->created_by  = UsniAdaptor::app()->user->getUserModel()->id;
        $elements = [
                            'username'        => ['type' => 'text'],
                            'status'          => UiHtml::getFormSelectFieldOptionsWithNoSearch(User::getStatusDropdown()),
                            'timezone'        => UiHtml::getFormSelectFieldOptions(TimezoneUtil::getTimezoneSelectOptions(),
                                                                                   [], ['placeholder' => UiHtml::getDefaultPrompt()]),
                            'groups'          => UiHtml::getFormSelectFieldOptions($this->getUserGroups(), [], ['multiple' => true]),
                            'type'            => ['type' => UiActiveForm::INPUT_HIDDEN, 'value' => 'system']
                    ];
        
        if($this->model->scenario == 'create' || $this->model->scenario == 'registration')
        {
            $elements['password']           = ['type' => UiActiveForm::INPUT_PASSWORD];
            $elements['confirmPassword']    = ['type' => UiActiveForm::INPUT_PASSWORD];
        }
        $metadata = [
                        'elements'              => $elements,
                    ];
        return $metadata;
    }
    
    /**
     * @inheritdoc
     */
    public function getExcludedAttributes()
    {
        if($this->model->scenario == 'registration' || $this->model->scenario == 'editprofile')
        {
            return ['status', 'timezone', 'groups'];
        }
        return [];
    }
    
    /**
     * Get user groups
     * @return array
     */
    public function getUserGroups()
    {
        $group              = new Group();
        $group->created_by  = UsniAdaptor::app()->user->getUserModel()->id;
        return $group->getMultiLevelSelectOptions('name', 0, '-', true, $this->shouldRenderOwnerCreatedModels());
    }
}