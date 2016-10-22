<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\widgets;

use usni\library\components\UiHtml;
use usni\fontawesome\FA;
use usni\library\utils\ArrayUtil;
use usni\UsniAdaptor;
/**
 * UiSortableMultiSelectList class file.
 * @package usni\library\widgets
 */
class UiSortableMultiSelectList extends \yii\widgets\InputWidget
{
    /**
     * @var array
     */
    public $listItems;
    /**
     * @var array
     */
    public $htmlOptions = array();
    /**
     * HTML options for navigator.
     * @var array
     */
    public $navigatorHtmlOptions = array();
    /**
     * @var string
     */
    public $listId;
    /**
     * @var bool
     */
    public $registerScript = false;
    /**
     * @var string
     */
    public $formId;
    /**
     * @var string
     */
    public $label;

    /**
     * Initialiazes the widget.
     * @return void
     */
    public function init()
    {
        if ($this->listId == null)
        {
            $this->listId = strtolower(UsniAdaptor::getObjectClassName(get_class($this->model)) . '-' . $this->attribute);
        }
        $this->label = $this->model->getAttributeLabel($this->attribute);
        if($this->registerScript)
        {
            $this->registerCoreScripts();
        }
        parent::init();
    }

    /**
     * Runs the widget.
     * @return void.
     */
    public function run()
    {
        $this->renderListBox();
        UiHtml::addCssClass($this->navigatorHtmlOptions, 'col-sm-1');
        echo UiHtml::tag('div', FA::icon('arrow-circle-up')->size(FA::SIZE_2X) .
                FA::icon('arrow-circle-down')->size(FA::SIZE_2X), $this->navigatorHtmlOptions);
    }

    /**
     * Renders list box.
     * @return string
     */
    private function renderListBox()
    {
        $htmlOptions                = $this->htmlOptions;
        $itemsContainerTemplate     = ArrayUtil::popValue('template', $htmlOptions);
        $htmlOptions['size']        = 10;
        $htmlOptions['multiple']    = true;
        $listbox                    = UiHtml::activeListBox($this->model, $this->attribute, $this->listItems, $htmlOptions);
        $error                      = UiHtml::tag('div', '',
                                                  array('class' => 'text-danger',
                                                        'id' => $this->listId . '_em',
                                                        'style' => 'display:none'));
        if ($itemsContainerTemplate != null)
        {
            echo UiHtml::tag('div', strtr($itemsContainerTemplate, array('{listbox}' => $listbox,
                                                       '{label}' => $this->label,
                                                       '{error}' => $error)) . "\n", ['class' => 'col-sm-9']);
        }
        else
        {
            echo UiHtml::tag('div', $listbox . "\n", ['class' => 'col-sm-9']);
        }
    }

    /**
     * Registers the core script code.
     * @return void
     */
    protected function registerCoreScripts()
    {
        $id             = $this->getId();
        $listId         = $this->listId;
        $listErrorContainerId = $listId . '_em';
        $script         = "";

        $script .= "$('body').on('click', '.fa-arrow-circle-up',function()
                        {
                            $('#{$listErrorContainerId}').html('');
                            $('#{$listErrorContainerId}').hide();
                            if ($('#" . $listId . " option:selected').first().index() > 0)
                            {
                                $('#" . $listId . " option:selected').each(function()
                                {
                                   $(this).insertBefore($(this).prev());
                                });
                            }
                        });
                   $('body').on('click', '.fa-arrow-circle-down',function()
                        {
                            $('#{$listErrorContainerId}').html('');
                            $('#{$listErrorContainerId}').hide();
                            if ($('#" . $listId . " option:selected').last().index() < ($('#" . $listId . " option').length - 1))
                            {
                                $($('#" . $listId . " option:selected').get().reverse()).map(function()
                                {
                                    if (!$(this).next().length) return false;
                                    $(this).insertAfter($(this).next());
                                });
                            }
                        });
                        ";
        //This would not be used in case of ajax so has to define at that place
        $script .= "$('#" . $this->formId . "').submit(function()
                            {
                                $('#" . $this->listId . " option').prop('selected', true);
                                return true;
                            });";
        $this->getView()->registerJs($script);
    }
}
?>