<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use yii\base\Model;
use usni\UsniAdaptor;
use usni\library\utils\ArrayUtil;
use yii\base\InvalidConfigException;
use usni\library\components\UiActiveForm;

/**
 * Abstract base class for form edit view.
 * 
 * @author Mayank Singhai <mayank.singhai@ushainformatique.com>
 * @package usni\library\views
 */
abstract class UiEditView extends UiView
{
    /**
     * ActiveForm instance associated with the view.
     * @var UiActiveForm
     */
    public $form;
    /**
     * Model associated with the form.
     * @var Model
     */
    public $model;

    /**
     * Controller associated with the form.
     * @var yii\base\Controller
     */
    public $controller;

    /**
     * Redirect url on save.
     * @var string
     */
    public $redirectUrl;

    /**
     * Class constructor.
     * @param array $config
     */
    public function __construct($config)
    {
        if(empty($config))
        {
            throw new InvalidConfigException();
        }
        //To support early implementation where model is passed
        elseif($config instanceof Model)
        {
            $this->model = $config;
            $this->controller = UsniAdaptor::app()->controller;
        }
        else
        {
            \Yii::configure($this, $config);
        }
    }

    /**
     * Renders content.
     * @return string
     */
    protected function renderContent()
    {
        $file = UsniAdaptor::getAlias($this->resolveFormViewPath()) . '.php';
        return $this->getView()->renderPhpFile($file, $this->resolveOutputData());
    }

    /**
     * Render begin of the form tag.
     * @return string
     */
    protected function renderBegin()
    {
        $options        = $this->resolveActiveFormWidgetOptions();
        $widgetClass    = static::getActiveFormWidgetClass();
        //Important: This is needed to register client scripts
        ob_start();
        $this->form     = $widgetClass::begin($options);
        $content        =  ob_get_clean();
        return $content;
    }

    /**
     * Renders description of the form.
     * @return string
     */
    protected function renderDescription()
    {
        return null;
    }

    /**
     * Renders error summary of the form.
     * @return string
     */
    public function renderErrorSummary()
    {
        $output = null;
        if($this->shouldRenderErrorSummary())
        {
			$output = $this->form->errorSummary($this->model, ['class' => 'alert alert-danger']);
        }
        return $output;
    }

    /**
     * Renders buttons of the form.
     * @param array $buttons
     * @return string
     */
    abstract protected function renderButtons($buttons);

    /**
     * Renders end of the form.
     * @return string
     * @see ActiveForm::renderEnd()
     */
    protected function renderEnd()
    {
        $widgetClass    = static::getActiveFormWidgetClass();
        ob_start();
        $widgetClass::end();
        return ob_get_clean();
    }

    /**
     * Gets active form widget layout.
     * @return string
     */
    public static function getFormLayout()
    {
        return null;
    }

    /**
     * Get form widget html options.
     * @return array
     */
    public function getActiveFormWidgetOptions()
    {
        return [];
    }

    /**
     * Resolve active form widget options.
     * @return array
     */
    public function resolveActiveFormWidgetOptions()
    {
        return ArrayUtil::merge($this->getDefaultActiveFormWidgetOptions(), $this->getActiveFormWidgetOptions());
    }

    /**
     * Get form widget html options.
     * @return array
     */
    public function getDefaultActiveFormWidgetOptions()
    {
        $options                = array('id'                        => static::getFormId(),
                                        'layout'                    => static::getFormLayout(),
                                        'enableAjaxValidation'      => false,
                                        'enableClientValidation'    => true,
                                        'validateOnSubmit'          => true,
                                        'errorCssClass'             => 'has-error',
                                        'successCssClass'           => 'has-success',
                                        'title'                     => $this->getTitle(),
                                        'showErrorSummary'          => $this->shouldRenderErrorSummary(),
                                        'action'                    => static::getAction(),
                                        'method'                    => static::getMethod(),
                                        'options'                   => array('class' => 'uiform'),
                            );
        if ($this->isMultiPartFormData())
        {
            $options['options']['enctype'] = 'multipart/form-data';
        }
        return $options;
    }

    /**
     * Gets active form widget class.
     * @return string
     */
    protected static function getActiveFormWidgetClass()
    {
        return UiActiveForm::className();
    }

    /**
     * Get form object.
     * @return ActiveForm
     */
    protected function getForm()
    {
        return $this->form;
    }

    /**
     * Renders title.
     * @return string
     */
    protected function renderTitle()
    {
        $model      = $this->model;
        $modelClass = get_class($model);
        if($model->scenario == 'create' || $model->scenario == 'update')
        {
            $prefix = $model->scenario == 'create' ? UsniAdaptor::t('application', 'Create') : UsniAdaptor::t('application', 'Update');
            return $prefix . ' ' . $modelClass::getLabel(1);
        }
        return $this->form->title;
    }

    /**
     * Gets form id.
     * @return string
     */
    public static function getFormId()
    {
        $view = get_called_class();
        $parts = explode('\\', $view);
        return strtolower($parts[count($parts) - 1]);
    }

    /**
     * Gets action for the form.
     * @return string
     */
    public static function getAction()
    {
        return '';
    }

    /**
     * Gets title for the form, it would be overridden if passed in the form title field in the metadata.
     * @return string | null
     */
    protected function getTitle()
    {
        return null;
    }

    /**
     * Gets method.
     * @return string
     */
    public static function getMethod()
    {
        return 'post';
    }

    /**
     * If form data multipart.
     * @return boolean
     */
    public function isMultiPartFormData()
    {
        return false;
    }

    /**
     * Gets form builder metadata.
     * @return void
     */
    public function getFormBuilderMetadata()
    {
        return [];
    }

    /**
     * Resolve form view path.
     * @return string
     */
    abstract public function resolveFormViewPath();

    /**
     * Resolve output data
     * @return array
     */
    public function resolveOutputData()
    {
        /*
         * Executing the form begin widget so that form object is created and
         * could be passed to the subviews.
         * @see usni\library\modules\users\views\ProfileEditView::renderSubView
         */
        $begin    = $this->renderBegin();
        $metadata = $this->getFormBuilderMetadata();
        $buttons  = null;
        if(ArrayUtil::getValue($metadata, 'buttons') != null)
        {
            $buttons = $this->renderButtons($metadata['buttons']);
        }
        return array(
            'begin'       => $begin,
			'title'       => $this->renderTitle(),
			'description' => $this->renderDescription(),
            'errorSummary'=> $this->renderErrorSummary(),
            'elements'    => $this->renderElements($metadata['elements']),
            'buttons'     => $buttons,
            'end'         => $this->renderEnd(),
            'form'        => $this->form,
            'model'       => $this->model,
            'callOut'     => $this->renderCallOut()
		);
    }
    
    /**
     * Should render error summary.
     * @return boolean
     */
    public function shouldRenderErrorSummary()
    {
        return false;
    }

    /**
     * Renders elements of the form.
     * @param array $elements
     * @return string
     */
    abstract protected function renderElements($elements);
    
    /**
     * Render call out
     * @return string
     */
    public function renderCallOut()
    {
        return null;
    }
}
?>