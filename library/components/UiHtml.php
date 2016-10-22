<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use yii\helpers\Html;
use usni\library\extensions\select2\ESelect2;
use usni\UsniAdaptor;
use usni\library\utils\ArrayUtil;

/**
 * UiHtml extends native Html functionality provided with yii2 framework.
 * @package usni\library\components
 */
class UiHtml extends Html
{
    const COLOR_DEFAULT     = 'default';
    const COLOR_PRIMARY     = 'primary';
    const COLOR_INFO        = 'info';
    const COLOR_SUCCESS     = 'success';
    const COLOR_WARNING     = 'warning';
    const COLOR_DANGER      = 'danger';

    /**
     * Gets form select field options.
     * @param array  $data
     * @param array $options
     * @param array  $htmlOptions Array of HtmlOptions e.g class, tag, params etc.
     * @return array
     */
    public static function getFormSelectFieldOptions($data, $options = [], $htmlOptions = [], $showDefault = false)
    {
        assert('is_array($data)');
        assert('is_array($htmlOptions)');
        if(YII_ENV == YII_ENV_TEST || $showDefault)
        {
            if(ArrayUtil::getValue($htmlOptions, 'multiple') == null)
            {
                return ['type' => 'dropdownList', 'items' => $data];
            }
            else
            {
                return ['type' => 'multiselect', 'items' => $data];
            }
        }
        else
        {
            return array(
                'type'          => UiActiveForm::INPUT_WIDGET,
                'class'         => ESelect2::className(),
                'data'          => $data,
                'options'       => $htmlOptions,
                'select2Options' => $options
            );
        }
    }

    /**
     * Gets form select field options with no search.
     * @param array  $data
     * @param array $select2Options passed to Eselect2 widgets
     * @param array  $options Array of options applied to select 2 dropdown
     * @return array
     */
    public static function getFormSelectFieldOptionsWithNoSearch($data, $select2Options = [], $options = [], $showDefault = false)
    {
        $select2Options['minimumResultsForSearch'] = -1;
        return self::getFormSelectFieldOptions($data, $select2Options, $options, $showDefault);
    }

    /**
     * Gets default prompt for the dropdown.
     * @return string
     */
    public static function getDefaultPrompt()
    {
        return UsniAdaptor::t('application', '--Select--');
    }

    /**
     * Create bootstrap button with a label.
     * @param string $buttonLabel Label of button.
     * @param string $id id of button.
     * @return array
     */
    public static function getSaveButtonElement($buttonLabel, $id = 'savebutton')
    {
        return array(
            'type'  => 'submit',
            'label' => $buttonLabel,
            'id'    => $id
        );
    }

    /**
     * Create submit button with a label for front.
     * @param string $buttonLabel Button Label.
     * @param string $class       Css Class.
     * @return array
     */
    public static function getFrontSubmitButtonElement($buttonLabel, $class = 'btn')
    {
        return array(
            'type' => 'submit',
            'label' => $buttonLabel,
            'class' => $class
        );
    }

    /**
     * Create link button with a label for front.
     *
     * @param string $buttonLabel Button Label.
     * @param string $url         Route Url.
     * @param string $params      Params.
     *
     * @return type
     */
    public static function getFrontLinkButtonElement($buttonLabel, $url, $params = array())
    {
        return array(
            'type' => 'link',
            'label' => $buttonLabel,
            'url' => createUrl($url, $params)
        );
    }

    /**
     * Gets form select field options.
     *
     * @param string $data        .
     * @param string $options     .
     * @param string $htmlOptions Html Options.
     *
     * @return array
     */
    public static function getFrontFormSelectFieldOptions($data, $options = array(), $htmlOptions = array())
    {
        assert('is_array($data)');
        assert('is_array($htmlOptions)');
        return array(
            'type'          => 'ext.select2.ESelect2',
            'data'          => $data,
            'htmlOptions'   => array_merge($htmlOptions, array('span' => 6)),
            'options'       => $options
        );
    }

    /**
     * Register select all and unselect all script
     * @param string $checkBoxSelectorSelectId
     * @param string $checkBoxSelectorUnselectClass
     * @param string $itemSelector
     * @param string $selectAllLabelClass
     * @param string $selectAllLabel
     * @param string $unselectAllLabel
     */
    public static function registerSelectUnselectAllScriptForCheckBox($checkBoxSelectorSelectClass,
                                                                      $checkBoxSelectorSelectId,
                                                                      $itemSelector, 
                                                                      $view)
    {
        $script = "$('body').on('click', '.{$checkBoxSelectorSelectClass}',
                     function(){
                                    var isChecked = $('#{$checkBoxSelectorSelectId}').is(':checked');
                                    if(isChecked)
                                    {
                                        $('.{$itemSelector}').prop('checked', true);
                                    }
                                    else
                                    {
                                        $('.{$itemSelector}').prop('checked', false);
                                    }
                                    
                                })";
        $view->registerJs($script);
    }

    /**
     * Get caret.
     * @return string
     */
    public static function caret()
    {
        return '<span class="caret"></span>';
    }

    /**
     * Wraps complete panel content.
     * @param string $output
     * @param array $htmlOptions
     * @return string
     */
    public static function panelContent($output, $htmlOptions = array())
    {
        $inputClass = ArrayUtil::getValue($htmlOptions, 'class');
        if($inputClass == null)
        {
            self::addCssClass($htmlOptions, 'panel panel-content');
        }
        return UiHtml::tag('div', $output, $htmlOptions);
    }
    
    /**
     * Makes panel body.
     * @param string $output
     * @param array $htmlOptions
     * @return string
     */
    public static function panelBody($output, $htmlOptions = array())
    {
        self::addCssClass($htmlOptions, 'panel-body');
        return UiHtml::tag('div', $output, $htmlOptions);
    }
    
    /**
     * Makes panel title.
     * @param string $title
     * @param array $htmlOptions
     * @return string
     */
    public static function panelTitle($title, $htmlOptions = [])
    {
        if($title != null)
		{
            self::addCssClass($htmlOptions, 'panel-title');
            $title = UiHtml::tag('h3', $title, $htmlOptions);
		}
        return UiHtml::tag('div', $title, ['class' => 'panel-heading']);
    }
}