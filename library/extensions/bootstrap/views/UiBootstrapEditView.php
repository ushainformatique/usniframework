<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\views;

use usni\UsniAdaptor;
use usni\library\views\UiEditView;
use usni\library\utils\ArrayUtil;
use usni\library\components\UiHtml;
use usni\library\components\UiActiveForm;
use usni\library\views\UiBrowseModelView;
use usni\library\modules\auth\managers\AuthManager;
/**
 * Bootstrap edit view.
 * 
 * @author Mayank Singhai <mayank.singhai@ushainformatique.com>
 * @package usni\library\extensions\bootstrap\views
 */
abstract class UiBootstrapEditView extends UiEditView
{
    /**
     * Gets active form widget layout.
     * @return string
     */
    public static function getFormLayout()
    {
        return 'horizontal';
    }
    
    /**
     * HTML output data for form elements.
     * @var string
     */
    protected $elementsOutputData = null;

    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $content    = null;
        $browseView = $this->renderEditModeBrowseView();
        $content   .= parent::renderContent();
        return $browseView . $content;
    }

    /**
     * @inheritdoc
     */
    protected function renderElements($elements)
    {
        $elementsOutputData  = array();
        foreach($elements as $attribute => $element)
        {
            $model =  $this->getModelForAttribute($attribute);
            //String element
            if(is_int($attribute))
            {
                $elementsOutputData[$attribute] = $element;
            }
            else
            {
                if(in_array($attribute, $this->getExcludedAttributes()))
                {
                    continue;
                }
                $type = ArrayUtil::getValue($element, 'type');
                if($type == null)
                {
                    throw new \yii\base\InvalidConfigException('Type is missing for attribute' . " $attribute");
                }
                if($type == UiActiveForm::INPUT_RAW)
                {
                    if(!array_key_exists('value', $element))
                    {
                        throw new \yii\base\InvalidConfigException();
                    }
                    $elementsOutputData[$attribute] = $element['value'];
                }
                elseif($type == UiActiveForm::INPUT_WIDGET)
                {
                    $elementsOutputData[$attribute] = $this->renderInputWidget($element, $model, $attribute);
                }
                elseif($type == UiActiveForm::INPUT_HIDDEN)
                {
                    if(($hiddenValue = ArrayUtil::getValue($element, 'value', false)) === false)
                    {
                        $elementsOutputData[$attribute] =  UiHtml::activeHiddenInput($model, $attribute);
                    }
                    else
                    {
                        $elementsOutputData[$attribute] =  UiHtml::activeHiddenInput($model, $attribute, ['value' => $hiddenValue]);
                    }
                }
                else
                {
                    $elementsOutputData[$attribute] = $this->renderElement($element, $model, $attribute, $type);
                }
            }
        }
        $this->setElementsOutputData($elementsOutputData);
        return $this->processRender();
    }
    
    /**
     * Get model for attribute
     * 
     * @param string $attribute
     * @return null|Model
     */
    public function getModelForAttribute($attribute)
    {
        $model = $this->model;
        $map = $this->getAttributeToModelMap();
        if(!empty($map))
        {
            $model = ArrayUtil::getValue($map, $attribute, $this->model);
        }
        return $model;
    }
    
    /**
     * Get model to attribute map
     * 
     * @return array
     */
    protected function getAttributeToModelMap()
    {
        return null;
    }


    /**
     * Render element.
     * @param array $element
     * @param Model $model
     * @param string $attribute
     * @param array $type
     * @return string
     */
    protected function renderElement($element, $model, $attribute, $type)
    {
        $element['name']    = $attribute;
        $options            = $this->resolveAttributeOptions($element, $model, $attribute);
        $extendedOptions    = $this->resolveAttributeExtendedOptions($element);
        $field            = $this->form->field($model, $attribute, $options);
        if($type == UiActiveForm::INPUT_RADIO_LIST && isset($element['items']))
        {
            $field = $field->radioList($element['items'], $extendedOptions);
        }
        elseif($type == UiActiveForm::INPUT_CHECKBOX_LIST && isset($element['items']))
        {
            $field = $field->checkboxList($element['items'], $extendedOptions);
        }
        elseif($type == UiActiveForm::INPUT_DROPDOWN_LIST && isset($element['items']))
        {
            $field = $field->dropDownList($element['items'], $extendedOptions);
        }
        elseif($type == UiActiveForm::INPUT_MULTISELECT && isset($element['items']))
        {
            $extendedOptions['multiple'] = 'multiple';
            $field = $field->listBox($element['items'], $extendedOptions);
        }
        elseif($type == UiActiveForm::INPUT_CHECKBOX)
        {
            $field = $field->checkbox($extendedOptions);
        }
        elseif($type == UiActiveForm::INPUT_PASSWORD)
        {
            $field = $field->passwordInput($extendedOptions);
        }
        elseif($type == UiActiveForm::INPUT_TEXTAREA)
        {
            $field = $field->textarea($extendedOptions);
        }
        elseif($type == UiActiveForm::INPUT_FILE)
        {
            $field = $field->fileInput($extendedOptions);
        }
        $this->renderElementHelp($model, $attribute, $type);
        return $field;
    }
    
    /**
     * Renders input widget.
     * @param array $element
     * @param Model $model
     * @param string $attribute
     * @return string
     */
    public function renderInputWidget($element, $model, $attribute)
    {
        if(YII_ENV == YII_ENV_TEST)
        {
            return $this->renderElement($element, $model, $attribute, $element['type']);
        }
        else
        {
            $class                  = $element['class'];
            $type                   = $element['type'];
            unset($element['type']);
            unset($element['class']);
            $element['name']        = $attribute;
            $options                = $this->resolveAttributeOptions($element, $model, $attribute);
            $field                  = $this->form->field($model, $attribute, $options)->widget($class, $element);
            $this->renderElementHelp($model, $attribute, $type);
            return $field;
        }
    }

    /**
	 * Renders the {@link buttons} in this form.
     * @param array $buttons
	 * @return string the rendering result
	 */
	protected function renderButtons($buttons)
	{
		$content = '';
		foreach($buttons as $id => $button)
        {
            $button['name']     = $id;
            $options            = $this->getFormButtonOptions($button);
            $options['name']    = $button['name'];
            if(ArrayUtil::getValue($button, 'id') == null)
            {
                $options['id']      = $id . '-button';
            }
            else
            {
                $options['id']      = $button['id'];
            }
            if($button['type'] == 'submit')
            {
                $content .= UiHtml::submitButton($button['label'], $options) . "\n";
            }
            elseif($button['type'] == 'link')
            {
                $content .= UiHtml::a($button['label'], $button['url'], $options) . "\n";
            }
            else
            {
                $content .= UiHtml::button($button['label'], $options) . "\n";
            }
        }
        $output  = array('{buttons}' => $content);
        return strtr($this->getButtonsWrapper(), $output);
	}

    /**
     * Process and get field options by key. These options would be the options for the ActiveField.
     * @param array $element
     * @param Model $model
     * @param string $attribute
     * @return string
     */
    protected function resolveAttributeOptions($element, $model, $attribute)
    {
        $defaultAttributeOptions  = $this->form->fieldConfig;
        $attributeOptions         = $this->attributeOptions();
        if(isset($attributeOptions[$element['name']]))
        {
            $keyOptions = ArrayUtil::getValue($attributeOptions, $element['name']);
            if($keyOptions != null)
            {
                return array_merge($defaultAttributeOptions, $keyOptions);
            }
        }
        if($model->isAttributeRequired($attribute))
        {
            //Reference: https://github.com/yiisoft/yii2/issues/2056
            $horizontalCssClasses = ArrayUtil::getValue($defaultAttributeOptions, 'horizontalCssClasses', null);
            if($horizontalCssClasses != null)
            {
                $defaultAttributeOptions['horizontalCssClasses']['label'] .= ' required'; 
            }
        }
        return $defaultAttributeOptions;
    }
    
    /**
     * Resolve attribute extended options. These option would be passed to the specific method for example checkbox
     * @param array $element
     * @return array
     */
    protected function resolveAttributeExtendedOptions($element)
    {
        $extendedOptions            = [];
        $attributeExtendedOptions   = $this->attributeExtendedOptions();
        if(isset($attributeExtendedOptions[$element['name']]))
        {
            $extendedOptions = $attributeExtendedOptions[$element['name']];
        }
        return $extendedOptions;
    }

    /**
     * Gets default attribute options.
     * @return array
     */
    protected function getDefaultAttributeOptions()
    {
        return array(
            'template'      => $this->getDefaultAttributeTemplate(),
            'options'       => ['class' => 'form-group'],
            'labelOptions'  => [],
            'inputOptions'  => [],
            'errorOptions'  => [],
            'hintOptions'   => [],
            'enableClientValidation'    => true,
            'enableAjaxValidation'      => false,
            'validateOnChange'          => true,
            'validateOnBlur'            => true,
            'validateOnType'            => false,
            'horizontalCssClasses'      => $this->getHorizontalCssClasses()
        );
    }

    /**
     * Get default attribute template.
     * @return string
     */
    protected function getDefaultAttributeTemplate()
    {
        return "{beginLabel}{labelTitle}{endLabel}{beginWrapper}{input}{error}{endWrapper}";
    }

    /**
     * Attribute options for the view. These options would be passed to the field object and not specific method e.g. checkbox
     * @return array
     */
    protected function attributeOptions()
    {
        return array();
    }
    
    /**
     * Extended attribute options for the field. These options would be passed to the specific method e.g. checkbox, textInput
     * @return array
     */
    protected function attributeExtendedOptions()
    {
        return array();
    }

    /**
     * Get form button options.
     * @param Array $element
     * @return array
     */
    protected function getFormButtonOptions($element)
    {
        $defaultButtonOptions  = $this->getDefaultButtonOptions();
        $buttonOptions         = $this->buttonOptions();
        if(($options = ArrayUtil::getValue($buttonOptions, $element['name'])) !== null)
        {
            return $options;
        }
        elseif(($options = ArrayUtil::getValue($defaultButtonOptions, $element['type'])) !== null)
        {
            return $options;
        }
        return array();
    }

    /**
     * Gets default button options.
     * @return array
     */
    protected function getDefaultButtonOptions()
    {
        return array(
            'htmlButton'    =>  array('class' => 'btn btn-primary'),
            'htmlSubmit'    =>  array('class' => 'btn btn-primary'),
            'htmlReset'     =>  array('class' => 'btn btn-default'),
            'button'        =>  array('class' => 'btn btn-primary'),
            'submit'        =>  array('class' => 'btn btn-primary'),
            'reset'         =>  array('class' => 'btn btn-default'),
            'image'         =>  array('class' => 'btn btn-success'),
            'link'          =>  array('class' => 'btn btn-default'),
            'ajaxSubmit'    =>  array('class' => 'btn btn-primary'),
        );
    }

    /**
     * Button options for the view.
     * @return array
     */
    protected function buttonOptions()
    {
        return array();
    }

    /**
     * Get buttons wrapper.
     * @return string
     */
    protected function getButtonsWrapper()
    {
        return "<div class='form-actions text-right'>{buttons}</div>";
    }

    /**
     * Get model dropdown list.
     * @return string
     */
    protected function renderEditModeBrowseView()
    {
        if($this->model->scenario == 'update')
        {
            $viewClassName = static::resolveBrowseModelViewClassName();
            $view          = new $viewClassName(
                                                [
                                                    'model' => $this->model, 
                                                    'attribute' => $this->resolveDefaultBrowseByAttribute(), 
                                                    'shouldRenderOwnerCreatedModelsForBrowse' => $this->shouldRenderOwnerCreatedModels()
                                                ]
                                                );
            return $view->render();
        }
        return null;
    }

    /**
     * Resolve default browse by attribute.
     * @return string
     */
    protected function resolveDefaultBrowseByAttribute()
    {
        return 'name';
    }

    /**
     * Should render owner created models for browse only. Thus if permission for update others
     * is true, this is false because user can see all the models along with owner models. If only update permission is there
     * than this is true as owner wants to see all his post.
     * @return boolean
     */
    protected function shouldRenderOwnerCreatedModels()
    {
        $user                   = UsniAdaptor::app()->user->getUserModel();
        $lowerModelClassName    =  strtolower(UsniAdaptor::getObjectClassName($this->model));
        if(AuthManager::checkAccess($user, $lowerModelClassName . '.updateother'))
        {
            return false;
        }
        return true;
    }

    /**
     * Resolve browse model view class name.
     * @return string
     */
    protected static function resolveBrowseModelViewClassName()
    {
        return UiBrowseModelView::className();
    }

    /**
     * Resolve form view path.
     * @return string
     */
    public function resolveFormViewPath()
    {
        return '@usni/themes/bootstrap/views/site/_form';
    }

    /**
     * Get horizontal css classes.
     * @return array
     */
    protected function getHorizontalCssClasses()
    {
        return [
                    'label'     => 'col-sm-2 ' . $this->getLabelAlignmentClass(),
                    'offset'    => '',
                    'wrapper'   => 'col-sm-10',
                    'error'     => '',
                    'hint'      => '',
               ];
    }
    
    /**
     * Get label alignment class
     * @return string
     */
    protected function getLabelAlignmentClass()
    {
        return null;
    }

    /**
     * Gets excluded attributes from rendering.
     * @return array
     */
    public function getExcludedAttributes()
    {
        return [];
    }
    
    /**
     * Renders element help.
     * @param Model $model
     * @param string $attribute
     */
    protected function renderElementHelp($model, $attribute, $type)
    {
        if(method_exists($model, 'attributeHints'))
        {
            $modelHints = $model->attributeHints();
            $hint       = null;
            if(!empty($modelHints))
            {
                $hint = ArrayUtil::getValue($modelHints, $attribute, null);
            }
            if($hint != null)
            {
                $id = UiHtml::getInputId($model, $attribute);
                if($type == UiActiveForm::INPUT_RADIO_LIST)
                {
                    $placement = 'left';
                }
                else
                {
                    $placement = 'top';
                }
                $script = "$('#{$id}').tooltip({'container':'body', 'trigger':'hover focus', 'placement':'{$placement}', 'title':'{$hint}'});";
                $this->getView()->registerJs($script, \yii\web\View::POS_END);
            }
        }
    }
    
    /**
     * Get elements output data.
     * @return array
     */
    public function getElementsOutputData()
    {
        return $this->elementsOutputData;
    }

    /**
     * Set elements output data.
     * @param string $elementsOutputData
     */
    public function setElementsOutputData($elementsOutputData)
    {
        $this->elementsOutputData = $elementsOutputData;
    }
    
    /**
     * Process render.
     * @return string
     */
    protected function processRender()
    {
        $content = null;
        foreach($this->getElementsOutputData() as $element => $html)
        {
            $content .= $html;
        }
        return $content;
    }
    
    /**
     * Override to register form submit script
     */
    protected function registerScripts()
    {
        $formId             = static::getFormId();
        $script             = "$('#{$formId}').on('beforeSubmit',
                                     function(event, jqXHR, settings)
                                     {
                                        var form = $(this);
                                        if(form.find('.has-error').length) {
                                                return false;
                                        }
                                        attachButtonLoader(form);
                                        return true;
                                     }
                                );";
        $this->getView()->registerJs($script);
    }
    
    /**
     * @inheritdoc
     */
    public function getActiveFormWidgetOptions()
    {
        return ['fieldConfig' => $this->getDefaultAttributeOptions()];
    }
}