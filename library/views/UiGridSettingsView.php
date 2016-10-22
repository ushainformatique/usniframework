<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\UsniAdaptor;
use usni\library\utils\ButtonsUtil;
use usni\library\components\UiHtml;
use usni\library\widgets\UiSortableMultiSelectCompareList;

/**
 * UiGridSettingsView class file.
 *
 * @package usni\library\views
 */
class UiGridSettingsView extends \usni\library\extensions\bootstrap\views\UiBootstrapModalEditView
{
    /**
     * Parent view.
     * @var string
     */
    protected $parentView;

    /**
     * Class constructor.
     * @param Model $model
     * @param Widget $parentView
     */
    public function __construct($model, $parentView)
    {
        $this->parentView  = $parentView;
        parent::__construct($model);
    }

    /**
     * Get form builder meta data.
     * @return array
     */
    public function getFormBuilderMetadata()
    {
        $compareWidget = $this->renderCompareListWidgetForColumns();
        $elements = array(
            'itemsPerPage'   => ['type' => 'text'],
            'viewClassName'  => ['type' => 'hidden'],
            $compareWidget,
            'modalDetailView'=> ['type' => 'checkbox'],
        );

        $metadata = array(
            'elements'  => $elements,
            'buttons'   => [
                'apply' => ButtonsUtil::getSubmitButton(UsniAdaptor::t('application', 'Apply')),
                'close' => array('type'  => 'htmlButton',
                                 'label' => UsniAdaptor::t('application', 'Close'))
            ]
        );

        return $metadata;
    }

    /**
     * @inheritdoc
     */
    protected function getTitle()
    {
        return UsniAdaptor::t('application', 'Screen Options');
    }

    /**
     * Renders compare list widget for page.
     * @return string
     */
    protected function renderCompareListWidgetForColumns()
    {
        $sourceTemplate = "<div class='col-xs-1'>&nbsp;</div><div class='col-xs-4'>{label}{listbox}{error}</div>";
        $targetTemplate = "<div class='col-xs-4'>{label}{listbox}{error}</div>";
        $rowTemplate    = "<div class='form-group'>{content}</div>";
        $availableCols  = $this->model->availableColumns;
        $displayedCols  = $this->model->displayedColumns;
        $availableCols  = array_combine($availableCols, $availableCols);
        $displayedCols  = array_combine($displayedCols, $displayedCols);
        //Reset so that columns doesn't get selected on load
        $this->model->availableColumns = [];
        $this->model->displayedColumns = [];
        $content = UiSortableMultiSelectCompareList::widget([
                                                                    'model'             => $this->model,
                                                                    'sourceAttribute'   => 'availableColumns',
                                                                    'targetAttribute'   => 'displayedColumns',
                                                                    'sourceItems'       => $availableCols,
                                                                    'targetItems'       => $displayedCols,
                                                                    'registerScript'    => true,
                                                                    'htmlOptions'       => [
                                                                                                'sourceTemplate' => $sourceTemplate,
                                                                                                'class'          => 'form-control',
                                                                                                'targetTemplate' => $targetTemplate
                                                                                           ],
                                                                    'formId'            => static::getFormId()
                                                                  ]);
        $content    = strtr($rowTemplate, array('{content}' => $content));
        return UiHtml::tag('div', $content, ["id" => "managecolumns"]);
    }

    /**
     * Button options for the view.
     * @return array
     */
    protected function buttonOptions()
    {
        return array('close'    => array('class' => 'btn btn-warning', 'data-dismiss' => 'modal'),
                     'apply'    => array('class' => 'btn btn-primary',
                                                                          'onclick' => "
                                                                            $('#gridviewoptionsform-displayedcolumns option').prop('selected', true);
                                                                            $('#gridviewoptionsform-availablecolumns option').prop('selected', true);
                                                                        "
                                                                        ));
    }

    /**
     * Get horizontal css classes.
     * @return array
     */
    protected function getHorizontalCssClasses()
    {
        $cssClasses             = parent::getHorizontalCssClasses();
        $cssClasses['label']    = 'col-sm-4 text-right';
        $cssClasses['wrapper']  = 'col-sm-6';
        return $cssClasses;
    }

    /**
     * Override to register form submit script
     */
    protected function registerScripts()
    {
        $formId         = static::getFormId();
        $sourceId       = $this->parentView->pjaxContainerId;
        $url            = $this->getGridSettingsViewUrl();
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
                                                                    $.fn.attachLoader('#{$formId}');
                                                                },
                                                    data: form.serialize()
                                                })
                                        .done(function(data, statusText, xhr){
                                                                if(data.status == 'failure')
                                                                {
                                                                    $.each(data.errors, function(index, errorMsgObj){
                                                                        $.each(errorMsgObj, function(k,v){
                                                                            $('#' + index + '_em').html(v);
                                                                            $('#' + index + '_em').show();
                                                                        });
                                                                    });
                                                                    $.fn.removeLoader('#{$formId}');
                                                                }
                                                                else
                                                                {
                                                                    $('#gridSettings').modal('toggle');
                                                                    //Timeout is critical here else pjax expires and
                                                                    //and page got refreshed
                                                                    $.pjax.reload({container:'#{$sourceId}', 'timeout':4000});
                                                                    $.fn.removeLoader('#{$formId}');
                                                                }
                                                              });

                                                return false;
                                     })";
        $this->getView()->registerJs($script);
    }
    
    /**
     * @inheritdoc
     */
    protected function attributeOptions()
    {
        return array(
            'modalDetailView' => array(
                    'options' => [],
                    'horizontalCheckboxTemplate' => "<div class=\"checkbox checkbox-admin\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n</div>\n{error}"
            )
        );
    }
    
    /**
     * Get buttons wrapper.
     * @return string
     */
    protected function getButtonsWrapper()
    {
        return "{buttons}";
    }
    
    /**
     * Get grid setting view url.
     * @return string
     */
    protected function getGridSettingsViewUrl()
    {
        $controllerId   = UsniAdaptor::app()->controller->id;
        $moduleId       = UsniAdaptor::app()->controller->module->getUniqueId();
        return UsniAdaptor::createUrl('/' . $moduleId . '/' . $controllerId . '/grid-view-settings');
    }
    
    /**
     * @inheritdoc
     */
    protected static function getModalId()
    {
        return 'gridSettings';
    }
    
    /**
     * @inheritdoc
     */
    protected static function getModelClassName()
    {
        return null;
    }
}