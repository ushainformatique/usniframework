<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\extensions\bootstrap\views\UiBootstrapBulkEditView;
use usni\library\components\UiHtml;
use usni\library\utils\TimezoneUtil;
use usni\library\utils\CountryUtil;
use usni\library\modules\auth\models\Group;
use usni\library\modules\users\models\User;
use usni\UsniAdaptor;
/**
 * UserBulkEditView class file.
 * @package usni\library\modules\users\views
 */
class UserBulkEditView extends UiBootstrapBulkEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $group              = new Group();
        $group->created_by  = UsniAdaptor::app()->user->getUserModel()->id;
        $elements = [
                            'status'          => UiHtml::getFormSelectFieldOptionsWithNoSearch(User::getStatusDropdown()),
                            'timezone'        => UiHtml::getFormSelectFieldOptions(TimezoneUtil::getTimezoneSelectOptions(),
                                                                                   [], ['placeholder' => UiHtml::getDefaultPrompt()]),
                            'groups'          => UiHtml::getFormSelectFieldOptions($group->getMultiLevelSelectOptions('name', 0, '-', true, $this->shouldRenderOwnerCreatedModels()), [], ['multiple' => true]),
                            'city'            => array('type' => 'text'),
                            'state'           => array('type' => 'text'),
                            'country'         => UiHtml::getFormSelectFieldOptions(CountryUtil::getCountries()),
                            'postal_code'     => array('type' => 'text'),
                    ];
        $metadata = [
                            'elements'              => $elements,
                            'buttons'               => $this->getSubmitButton()
                    ];
        return $metadata;
    }
    
    /**
     * @inheritdoc
     */
    protected function getTitle()
    {
         return UsniAdaptor::t('application', 'Bulk Edit');
    }
    
    /**
     * @inheritdoc
     */
    protected function attributeOptions()
    {
        return [
            'city'          => ['inputOptions'  => ['disabled' => 'disabled']],
            'state'         => ['inputOptions'  => ['disabled' => 'disabled']],
            'postal_code'   => ['inputOptions'  => ['disabled' => 'disabled']],
            'firstname'     => ['inputOptions'  => ['disabled' => 'disabled']],
            'lastname'      => ['inputOptions'  => ['disabled' => 'disabled']],
        ];
    }
}