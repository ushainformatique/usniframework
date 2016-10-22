<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\library\components\UiHtml;
use yii\helpers\Json;
use yii\base\InvalidCallException;
use yii\widgets\ActiveFormAsset;
/**
 * Override yii2 bootstrap ActiveForm class for changes specific to framework.
 * 
 * @author Mayank Singhai <mayank.singhai@ushainformatique.com>
 * @package usni\library\components
 */
class UiActiveForm extends \yii\bootstrap\ActiveForm
{
    // form inputs
    const INPUT_HIDDEN = 'hidden';
    const INPUT_TEXT = 'text';
    const INPUT_TEXTAREA = 'textarea';
    const INPUT_PASSWORD = 'password';
    const INPUT_DROPDOWN_LIST = 'dropdownList';
    const INPUT_LIST_BOX = 'listBox';
    const INPUT_CHECKBOX = 'checkbox';
    const INPUT_RADIO = 'radio';
    const INPUT_CHECKBOX_LIST = 'checkboxList';
    const INPUT_RADIO_LIST = 'radioList';
    const INPUT_MULTISELECT = 'multiselect';
    const INPUT_STATIC = 'staticInput';
    const INPUT_FILE = 'fileInput';
    const INPUT_HTML5 = 'input';
    const INPUT_WIDGET = 'widget';
    const INPUT_RAW = 'raw'; // any free text or html markup

    /**
     * @var array the allowed valid list of input types
     */
    protected static $_validInputs = [
        self::INPUT_HIDDEN,
        self::INPUT_TEXT,
        self::INPUT_TEXTAREA,
        self::INPUT_PASSWORD,
        self::INPUT_DROPDOWN_LIST,
        self::INPUT_LIST_BOX,
        self::INPUT_CHECKBOX,
        self::INPUT_RADIO,
        self::INPUT_CHECKBOX_LIST,
        self::INPUT_RADIO_LIST,
        self::INPUT_MULTISELECT,
        self::INPUT_STATIC,
        self::INPUT_FILE,
        self::INPUT_HTML5,
        self::INPUT_WIDGET,
        self::INPUT_RAW
    ];

    /**
     * @var string the default field class name when calling [[field()]] to create a new field.
     * @see fieldConfig
     */
    public $fieldClass = 'usni\library\components\UiActiveField';
    /**
     * Title of the form.
     * @var string
     */
    public $title;
    /**
     * Show error summary
     * @var boolean
     */
    public $showErrorSummary;

    /**
     * Initializes the widget.
     * This renders the form open tag.
     */
    public function init()
    {
        if(!$this->showErrorSummary)
        {
            $this->errorSummaryCssClass = 'error-summary hide';
        }
        parent::init();
        //Clean so that ob_start in parent is closed
        ob_end_clean();
        echo UiHtml::beginForm($this->action, $this->method, $this->options);
    }
    
    /**
     * @inheritdoc
     * 
     * Override so that we don't have to change the code based on upgraded version.
     */
    public function run()
    {
        if (!empty($this->_fields)) 
        {
            throw new InvalidCallException('Each beginField() should have a matching endField() call.');
        }

        if ($this->enableClientScript) 
        {
            $id = $this->options['id'];
            $options = Json::htmlEncode($this->getClientOptions());
            $attributes = Json::htmlEncode($this->attributes);
            $view = $this->getView();
            ActiveFormAsset::register($view);
            $view->registerJs("jQuery('#$id').yiiActiveForm($attributes, $options);");
        }

        echo UiHtml::endForm();
    }
}