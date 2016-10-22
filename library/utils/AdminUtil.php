<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\UsniAdaptor;
use usni\library\components\UiHtml;
use usni\library\modules\users\models\User;
use usni\library\modules\auth\managers\AuthManager;
use usni\fontawesome\FA;
use usni\library\utils\FileUploadUtil;
/**
 * Class consisting of utility functions related to admin.
 * 
 * @package usni\library\utils
 */
class AdminUtil
{
    /**
     * Get list of parent menu items for front end.
     * @param array $sortOrderData
     * @return array
     */
    public static function getAdminMenuItemsList($sortOrderData = array())
    {
        $items         = array();
        $sortedItems   = array();
        $modules = UsniAdaptor::app()->moduleManager->getInstantiatedModules();
        foreach ($modules as $key => $module)
        {
            if($key == 'debug')
            {
                continue;
            }
            $menuConfigFile = $module->getMenuManager();
            if(class_exists($menuConfigFile))
            {
                $items[$key] = ucfirst($module->id);
            }
        }
        //Sort the items here
        if(!empty($sortOrderData))
        {
            foreach ($sortOrderData as $sortOrderId)
            {
                if(!empty($items))
                {
                    $sortedItems[$sortOrderId] = $items[$sortOrderId];
                    unset($items[$sortOrderId]);
                }
            }
            $sortedItems = ArrayUtil::merge($sortedItems, $items);
        }
        else
        {
            $sortedItems = $items;
        }
        return $sortedItems;
    }

    /**
     * Get Yes No Select options.
     * @return array
     */
    public static function getYesNoOptions()
    {
        return [
                    1 => UsniAdaptor::t('application', 'Yes'),
                    0 => UsniAdaptor::t('application', 'No'),
               ];
    }
    
    /**
     * Get yes no option display text
     * @param int $value
     * @return string
     */
    public static function getYesNoOptionDisplayText($value)
    {
        $options = static::getYesNoOptions();
        return $options[$value];
    }
    
    /**
     * Does user have other permissions on the model.
     * @param string $modelClassName
     * @param User $user
     * @return boolean
     */
    public static function doesUserHaveOthersPermissionsOnModel($modelClassName, $user)
    {
        $lowerModelClassname = strtolower(UsniAdaptor::getObjectClassName($modelClassName));
        if(!AuthManager::checkAccess($user, $lowerModelClassname . '.updateother')
            && !AuthManager::checkAccess($user, $lowerModelClassname . '.viewother')
               && !AuthManager::checkAccess($user, $lowerModelClassname . '.deleteother'))
        {
            return false;
        }
        return true;
    }
    
    /**
     * Gets options for the model.
     * @param string $modelClassName
     * @param User $user
     * @return array
     */
    public static function getTranslatableModelSelectOptions($modelClassName)
    {
        $user       = UsniAdaptor::app()->user->getUserModel();
        $shouldRenderOnlyOwnerCreatedModels  = !(AdminUtil::doesUserHaveOthersPermissionsOnModel($modelClassName, $user));
        $tableName  = $modelClassName::tableName();
        if($shouldRenderOnlyOwnerCreatedModels && $user != null)
        {
            $models = $modelClassName::find()->innerJoinWith('translations')->where($tableName . '.created_by = :cBy ', [':cBy' => $user->id])->orderBy(['name' => SORT_ASC])->all();
        }
        else
        {
            $models = $modelClassName::find()->innerJoinWith('translations')->orderBy(['name' => SORT_ASC])->all();
        }
        if(empty($models))
        {
            return ['' => UiHtml::getDefaultPrompt()];
        }
        return ArrayUtil::map($models, 'id', 'name');
    }
    
    /**
     * Generate password reset token.
     * @param type int $id
     * @return string
     */
    public static function getPasswordResetToken($id)
    {
        $token  = '';
        $user   = User::findOne($id);
        if($user->password_reset_token != '')
        {
            $token = $user->password_reset_token;
        }
        else
        {
            $user->generatePasswordResetToken();
            $user->save();
            $token = $user->password_reset_token;
        }
        return $token;
    }
    
    /**
     * Get modified selected ids for bulk edit
     * @param array $excludedSelectedIds
     * @param array $selectedIdData
     * @return array
     */
    public static function getModifiedSelectedIdsForBulkEdit($excludedSelectedIds, $selectedIdData)
    {
        $modifiedSelectedIds = array();
        if(!empty($excludedSelectedIds))
        {
            foreach($selectedIdData as $id)
            {
                if(!in_array($id, $excludedSelectedIds))
                {
                    $modifiedSelectedIds[] = $id;
                }
            }
        }
        else
        {
            $modifiedSelectedIds = $selectedIdData;
        }
        return $modifiedSelectedIds;
    }
    
    /**
     * Add dropdown option script on quick create.
     * @param string $url
     * @param string $formId
     * @param string $modelClassName
     * @param string $targetDropDownId
     * @param \yii\web\View the view object that can be used to render views or view files  $targetDropDownId
     * @return string
     */
    public static function addDropdownOptionScriptOnQuickCreate($url, $formId, $modelClassName, $targetDropDownId, $view, $modalId)
    {
        $script         = "$('#{$formId}').on('beforeSubmit',
                                     function(event, jqXHR, settings)
                                     {
                                        var form = $(this);
                                        if(form.find('.has-error').length) {
                                                return false;
                                        }
                                        $.ajax({
                                                    url: '{$url}',
                                                    type: 'post',
                                                    dataType: 'json', 
                                                    beforeSend: function()
                                                                {
                                                                    $('.alert-success').hide();
                                                                },
                                                    data: form.serialize(),
                                                    'success'  : function(data)
                                                                         {
                                                                            if(data.status == 'failure')
                                                                            {
                                                                              $.fn.renderAjaxErrors(data.errors, '{$modelClassName}', 'has-error',
                                                                                                      'has-success', '{$formId}', '.form-group');
                                                                            }
                                                                            else
                                                                            {
                                                                              $('#{$modalId}').modal('toggle');
                                                                              $('#{$targetDropDownId}').parent().find('.select2-container').addClass('grid-view-loader');
                                                                              var newOption = '<option value=\"' + data.id + '\">' + data.value + '</option>';
                                                                              $('#{$targetDropDownId}').append(newOption);
                                                                              $('#{$targetDropDownId}').parent().find('.select2-container').removeClass('grid-view-loader');
                                                                            }
                                                                            removeButtonLoader(form);
                                                                          },
                                                            });
                                                return false;
                                     })";
        $view->registerJs($script, \yii\web\View::POS_END);
    }
    
    /**
     * @inheritdoc
     */
    public static function registerCancleModalViewScripts($view, $modalId)
    {
        $view->registerJs("
                            $('body').on('click', '#cancel-quickCreateModal',
                            function()
                            {
                                $('#{$modalId}').modal('toggle');
                                return false;
                             });
                          ", \yii\web\View::POS_END);
    }
    
    /**
     * Renders thumbnail
     * @param string $actionId
     * @param Model $model.
     * @param string $attribute Image attribute.
     * @param array $htmlOptions. It could contain width and height of the required image
     * @return string
     */
    public static function renderThumbnail($model, $attribute, $htmlOptions = [])
    {
        if ($model->isNewRecord === false && $model->$attribute != null)
        {
            $thumbnail  = FileUploadUtil::getThumbnailImage($model, $attribute, $htmlOptions);
            $icon       = FA::icon('trash');
            $title      = UsniAdaptor::t('application', 'Delete Image');
            $deleteLink = UiHtml::a($icon, '#', ['class' => 'delete-image', 'title' => $title]);
            return UiHtml::tag('div', $thumbnail . $deleteLink, ['class' => 'image-thumbnail']);
        }
        return null;
    }
    
    /**
     * @inheritdoc
     */
    public static function registerDeleteImageScripts($modelId, $url, $modelClassName, $view)
    {
        $id             = $modelId;
        $modelClassName = base64_encode($modelClassName);
        $script = "$('.delete-image').click(function(){
                                                    $.ajax({
                                                            'type':'GET',
                                                            'url':'{$url}' + '?id=' + '{$id}',
                                                            'data': 'modelClass=' + '{$modelClassName}',
                                                            'success':function(data)
                                                                      {
                                                                          $('.image-thumbnail').load(location.href + ' .image-thumbnail');
                                                                      }
                                                          });
                                                 });";
        $view->registerJs($script);
    }
    
    /**
     * Wrap admin sidebar menu label.
     * @param string $label
     * @return string.
     */
    public static function wrapSidebarMenuLabel($label)
    {
        return UiHtml::tag('span', $label);
    }
}