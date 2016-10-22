<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\views;

use usni\UsniAdaptor;
use usni\library\components\UiHtml;
use usni\library\utils\ButtonsUtil;
use usni\library\utils\ArrayUtil;
/**
 * UiBootstrapBulkEditView class file.
 * 
 * @package usni\library\extensions\bootstrap\views
 */
abstract class UiBootstrapBulkEditView extends UiBootstrapEditView
{
    /**
     * Grid view id.
     * @var string
     */
    protected $gridViewId;

    /**
     * Pjax id.
     * @var string
     */
    protected  $pjaxId;
    /**
     * Class constructor.
     *
     * @param array  $model
     * @param string $selectedIds
     */
    public function __construct($model, $gridView, $pjaxId)
    {
        $model->scenario    = 'bulkedit';
        $this->gridViewId   = $gridView;
        $this->pjaxId       = $pjaxId;
        parent::__construct($model);
    }

    /**
     * Register the scripts.
     */
    protected function registerScripts()
    {
        $gridViewId = $this->gridViewId;
        $sourceId   = $this->pjaxId;
        $alert      = UsniAdaptor::t('application', 'Please select records to update.');
        $formId     = strtolower(UsniAdaptor::getObjectClassName($this->getId()));
        $url        = $this->getBulkEditUrl();
        $ajaxError  = UsniAdaptor::t('application', 'There is an error in processing the ajax request.');
        $this->getView()->registerJs("
            $('.bulk-edit-btn').click(function(){
                $('.bulk-edit-form').toggle();
                return false;
            });
            $('#{$formId} .selectBulkEdit').click(function(){
                        if($(this).is(':checked'))
                        {
                            var checkedId = $(this).attr('data-id');
                            console.log(checkedId);
                            console.log($('#{$formId} #'+checkedId));
                            $('#{$formId} #'+checkedId).prop('disabled',false);
                        }
                        else
                        {
                            var checkedId = $(this).attr('data-id');
                            $('#{$formId} #'+checkedId).prop('disabled',true);
                        }
                       });
            $('body').on('click', '.grid-bulk-edit-btn', function(data){
                    var idList      = $('#{$gridViewId}').yiiGridView('getSelectedRows');
                    if(idList == '')
                    {
                        return false;
                    }
                    var paramStr    = '&selectedIds=' + idList;
                    $.ajax({
                                'type'     : 'POST',
                                'dataType' : 'html',
                                'url'      : '{$url}'+ paramStr,
                                'data'     : $('#{$formId}').serialize(),
                                'beforeSend' : function()
                                               {
                                                    $.fn.attachLoader('#{$formId}');
                                               },
                                'success'  : function(data)
                                              {
                                                $.pjax.reload({container:'#{$sourceId}', 'timeout':10000});
                                                $.fn.removeLoader('#{$formId}');
                                                $('.bulk-edit-form').toggle();
                                              },
                                error     : function(data)
                                            {
                                                $.fn.removeLoader('#{$formId}');
                                            }
                               });
                    return false;
                });
           ");
    }

    /**
     * Button options for the view.
     * @return array
     */
    protected function buttonOptions()
    {
        return array(
            'submit' => array('class' => 'grid-bulk-edit-btn btn btn-primary')
        );
    }

    /**
     * Gets bulk edit url.
     * @return string
     */
    protected function getBulkEditUrl()
    {
        return UsniAdaptor::createUrl('/' . $this->getModule() . '/' . $this->controller->id . '/bulk-edit', ['modelClassName' => get_class($this->model)]);
    }

    /**
     * Get Module Name.
     * @return string
     */
    protected function getModule()
    {
        return $this->controller->module->getUniqueId();
    }
    
    /**
     * Gets submit button metadata.
     * @return array
     */
    protected function getSubmitButton()
    {
        return array(
                        'submit' => ButtonsUtil::getSubmitButton(UsniAdaptor::t('application', 'Submit'))
                    );
    }

     /**
     * Get default attribute template.
     * @return string
     */
    protected function getDefaultAttributeTemplate()
    {
        return "{checkbox}{beginLabel}\n{labelTitle}\n{endLabel}\n{beginWrapper}\n{input}{endWrapper}";
    }

    /**
     * Gets default attribute options.
     * @return array
     */
    protected function getDefaultAttributeOptions()
    {
        $attributeOptions = parent::getDefaultAttributeOptions();
        $attributeOptions['labelOptions'] = ['class' => 'control-label col-xs-1'];
        return $attributeOptions;
    }

    /**
     * @inheritdoc
     */
    public function renderInputWidget($element, $model, $attribute)
    {
        $element['name']   = $attribute;
        $options           = $this->resolveAttributeOptions($element, $model, $attribute);
        $disabledOption    = ArrayUtil::getValue($options, 'disabled', null);
        if($disabledOption == null)
        {
            $element['options']['disabled'] = 'disabled';
        }
        $content = parent::renderInputWidget($element, $model, $attribute);
        return $this->renderContentWithCheckbox($content, $element, $model, $attribute);
    }
    
    /**
     * @inheritdoc
     */
    protected function renderElement($element, $model, $attribute, $type)
    {
        $content = parent::renderElement($element, $model, $attribute, $type);
        return $this->renderContentWithCheckbox($content, $element, $model, $attribute);
    }
    
    /**
     * Renders content with checkbox
     * @param string $content
     * @param array $element
     * @param Model $model
     * @param string $attribute
     * @return string
     */
    protected function renderContentWithCheckbox($content, $element, $model, $attribute)
    {
        $activeId   = UiHtml::getInputId($model, $attribute);
        $checkBox   = UiHtml::checkBox('chk_' . $activeId, false, ['data-id' => $activeId, 'class' => 'selectBulkEdit']);
        $checkBox   = UiHtml::tag('div', $checkBox, ['class' => 'col-xs-1 checkbox bulk-edit-chk']);
        $content    = str_replace('{checkbox}', $checkBox, $content);
        return $content;
    }
    
    /**
     * @inheritdoc
     */
    protected function attributeOptions()
    {
        $options    = [];
        $metadata   = $this->getFormBuilderMetadata();
        $attributes = array_keys($metadata['elements']);
        foreach($attributes as $attribute)
        {
            $options[$attribute] = ['inputOptions' => ['disabled' => 'disabled']];
        }
        return $options;
    }
}
?>