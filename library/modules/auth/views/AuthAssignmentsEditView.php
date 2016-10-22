<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\views;

use usni\library\extensions\bootstrap\views\UiBootstrapEditView;
use usni\library\utils\ButtonsUtil;
use usni\UsniAdaptor;
use usni\library\components\UiHtml;
use usni\library\modules\auth\models\Group;
use usni\library\modules\users\utils\UserDropDownUtil;
use usni\library\modules\auth\views\AuthPermissionsSelectView;
use usni\library\utils\ArrayUtil;
use usni\library\utils\FlashUtil;
use usni\library\modules\users\models\User;
/**
 * AuthAssignmentsEditView class file.
 * @package usni\library\modules\auth\views
 */
class AuthAssignmentsEditView extends UiBootstrapEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $authType           = $this->model->authType;
        if($authType == 'group')
        {
            $group   = new Group();
            $options = $group->getMultiLevelSelectOptions('name');
            $cancelUrl = '/auth/group/manage';
        }
        else
        {
            $options = UserDropDownUtil::getSelectOptions();
            $cancelUrl = '/users/default/manage';
        }
        $elements = [
                        'authIdentityId'  => UiHtml::getFormSelectFieldOptions($options),
                        'permissions'     => ['type' => 'checkboxlist']
                    ];
        $metadata = [
                        'elements' => $elements,
                        'buttons'  =>  [
                                             'savepermissions' => ButtonsUtil::getSubmitButton(UsniAdaptor::t('application', 'Save'),
                                                                                                   null),
                                             'cancel' => ButtonsUtil::getCancelLinkElementData($cancelUrl)
                                       ]
                    ];

        return $metadata;
    }

    /**
     * @inheritdoc
     */
    protected function renderTitle()
    {
        return UsniAdaptor::t('auth', 'Manage Permissions');
    }

    /**
     * Process and render input element.
     * @param array $elements
     * @return string
     */
    protected function renderElements($elements)
    {
        $content = null;
        foreach($elements as $name => $element)
        {
            if($name == 'permissions')
            {
                $content         .= '<hr/>';
                $selectionView    = new AuthPermissionsSelectView($this->model);
                $content         .= UiHtml::tag('div', $selectionView->render(), ['id' => 'permissionsSelect', 'class' => 'permissionsContainer']);
                UiHtml::registerSelectUnselectAllScriptForCheckBox('selectAllCheckBoxLabel',
                                                                   'selectAllChk',
                                                                   'authitem',
                                                                   $this->getView());
            }
            else
            {
                $content .= parent::renderInputWidget($element, $this->model, $name);
            }
        }
        return $content;
    }

    /**
     * Renders flash messages.
     * @return string
     */
    protected function renderFlashMessages()
    {
        return FlashUtil::render('savepermissions', 'alert alert-success hidden');
    }

    /**
     * Registers script.
     * @return void
     */
    protected function registerScripts()
    {
        self::registerPermissionListScript($this->getView());
        self::registerSavePermissionsScript($this->getView());
    }
    
    /**
     * @inheritdoc
     */
    protected function buttonOptions()
    {
        return array(
                'savepermissions' => array('class' => 'btn btn-primary', 'id' => 'savepermissions'),
            );
    }
    
    /**
     * @inheritdoc
     */
    protected function attributeOptions()
    {
        $authType           = $this->model->authType;
        if($authType == 'group')
        {
            $label = Group::getLabel(1);
        }
        else
        {
            $label = User::getLabel(1);
        }
        $attributeOptions = parent::attributeOptions();
        return ArrayUtil::merge($attributeOptions, array(
            'authIdentityId' => array(
                    'labelOptions'  => ['label' => $label]
            )
        ));
    }
    
    /**
     * Register permissions list script.
     * @return void
     */
    protected static function registerPermissionListScript($view)
    {
        $formId = static::getFormId();
        $url    = UsniAdaptor::createUrl('/auth/permission/list');
        $script = "$('body').on('change',
                                '#authassignmentform-authidentityid',
                                function(e){
                                    $.ajax({
                                        type : 'GET',
                                        data : {'authIdentityId':e.val},
                                        url  : '" . $url . "',
                                        beforeSend : function(){
                                                        $.fn.attachLoader('#{$formId}');
                                                    },
                                        success : function(data){
                                                    $('#permissionsSelect').html(data);
                                                    $.fn.removeLoader('#{$formId}');
                                                  }
                                    });
                                });";
        $view->registerJs($script);
    }
    
    /**
     * Save permissions for group.
     * @return void
     */
    protected static function registerSavePermissionsScript($view)
    {
        $formId = static::getFormId();
        $id     = $_GET['id'];
        $url    = UsniAdaptor::createUrl('/auth/permission/group?id='.$id);
        $view->registerJs("
                            $('body').on('click', '#savepermissions-button',
                            function()
                            {
                              $.ajax({
                                 'type' : 'post',
                                 'url'  : '" . $url . "',
                                 'data' : $('#$formId').serialize(),
                                 'beforeSend' : function()
                                                {
                                                  $('#savepermissions').addClass('hidden');
                                                  $.fn.attachLoader('#{$formId}');
                                                },
                                 'success' : function(data)
                                             {
                                                $('#savepermissions').removeClass('hidden');
                                                $('.alert-success').removeClass('hidden');
                                                $.fn.removeLoader('#{$formId}');
                                             }
                              });
                              return false;
                             });
                          ");
    }
}
?>