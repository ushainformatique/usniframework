<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\widgets;

use usni\fontawesome\FA;
use usni\library\components\UiHtml;
use usni\library\utils\ArrayUtil;

/**
 * UiSortableMultiSelectCompareList class file.
 * @package usni\library\widgets
 */
class UiSortableMultiSelectCompareList extends \yii\bootstrap\Widget
{
    /**
     * @var string
     */
    public $sourceAttribute;
    /**
     * @var string
     */
    public $targetAttribute;
    /**
     * @var array
     */
    public $sourceItems;
    /**
     * @var array
     */
    public $targetItems;
    /**
     * @var Model
     */
    public $model;
    /**
     * @var array
     */
    public $htmlOptions = array();
    /**
     * @var string
     */
    public $leftListId;
    /**
     * @var string
     */
    public $rightListId;
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
    public $sourceLabel;
    /**
     * @var string
     */
    public $targetLabel;
    
    /**
     * Source template for the list
     * @var string 
     */
    public $sourceTemplate;
    
    /**
     * Target template for the list
     * @var string 
     */
    public $targetTemplate;

    /**
     * Initializes the widget.
     * @return void
     */
    public function init()
    {
        if ($this->rightListId == null)
        {
            $this->rightListId = UiHtml::getInputId($this->model, $this->targetAttribute);
        }
        if ($this->leftListId == null)
        {
            $this->leftListId  = UiHtml::getInputId($this->model, $this->sourceAttribute);
        }
        $this->sourceLabel = $this->model->getAttributeLabel($this->sourceAttribute);
        $this->targetLabel = $this->model->getAttributeLabel($this->targetAttribute);
        if($this->registerScript)
        {
            $this->registerCoreScripts();
        }
    }

    /**
     * Renders content.
     * @return string.
     */
    public function run()
    {
        echo $this->renderSourceListBox().
             $this->renderNavIcons() .
             $this->renderTargetListBox() .
             $this->renderVerticalNavIcons();
    }

    /**
     * Renders source list box.
     * @return void
     */
    protected function renderSourceListBox()
    {
        $this->sourceTemplate  = ArrayUtil::popValue('sourceTemplate', $this->htmlOptions);
        $this->targetTemplate  = ArrayUtil::popValue('targetTemplate', $this->htmlOptions);
        return $this->renderListBox($this->sourceAttribute,
                                                    $this->sourceItems,
                                                    $this->htmlOptions,
                                                    $this->model,
                                                    $this->leftListId,
                                                    $this->sourceLabel,
                                                    $this->sourceTemplate);

    }

    /**
     * Renders nav icons.
     * @return string
     */
    protected function renderNavIcons()
    {
        $rightIcon = FA::icon('arrow-circle-right')->size(FA::SIZE_2X);
        $leftIcon = FA::icon('arrow-circle-left')->size(FA::SIZE_2X);
        return UiHtml::tag('div', $rightIcon . '<br/>' . $leftIcon, ['class' => 'col-xs-1 compare-list-actions']);
    }

    /**
     * Renders target list box.
     * @return void
     */
    protected function renderTargetListBox()
    {
        return $this->renderListBox($this->targetAttribute,
                                                    $this->targetItems,
                                                    $this->htmlOptions,
                                                    $this->model,
                                                    $this->rightListId,
                                                    $this->targetLabel,
                                                    $this->targetTemplate);


    }

    /**
     * Renders nav icons.
     * @return string
     */
    protected function renderVerticalNavIcons()
    {
        $upIcon     = FA::icon('arrow-circle-up')->size(FA::SIZE_2X);
        $downIcon   = FA::icon('arrow-circle-down')->size(FA::SIZE_2X);
        return UiHtml::tag('div', $upIcon .  '<br/>' . $downIcon, ['class' => 'col-xs-1 compare-list-actions']);
    }

    /**
     * Renders list box.
     * @param string $attribute
     * @param array $items
     * @param array $htmlOptions
     * @param Model $model
     * @param string $listId
     * @param string $label
     */
    private function renderListBox($attribute,
                                   $items,
                                   $htmlOptions,
                                   $model,
                                   $listId,
                                   $label,
                                   $itemsContainerTemplate)
    {
        $htmlOptions['size']        = 10;
        $htmlOptions['multiple']    = true;
        $listbox                    = UiHtml::activeListBox($model, $attribute, $items, $htmlOptions);
        $error                      = UiHtml::tag('div', '',
                                                  ['class' => 'text-danger',
                                                   'id' => $listId . '_em',
                                                   'style' => 'display:none']);
        if ($itemsContainerTemplate != null)
        {
            return strtr($itemsContainerTemplate, array('{listbox}' => $listbox,
                                                       '{label}' => $label,
                                                       '{error}' => $error)) . "\n";
        }
        else
        {
            return $listbox . "\n";
        }
    }

    /**
     * Registers the core script code.
     * @return void
     */
    protected function registerCoreScripts()
    {
        $id             = $this->getId();
        $leftListId     = $this->leftListId;
        $rightListId    = $this->rightListId;
        $targetLabel    = $this->targetLabel;
        $rightListErrorContainerId = $rightListId . '_em';
        $script         = "";

        $script .= "$('body').on('click', '.compare-list-actions .fa-arrow-circle-right',function()
                        {
                            $('#{$rightListErrorContainerId}').html('');
                            $('#{$rightListErrorContainerId}').hide();
                            return !$('#" . $leftListId . " option:selected')
                            .remove().appendTo('#" . $rightListId . "');
                        });
                    $('body').on('click', '.compare-list-actions .fa-arrow-circle-left', function()
                        {
                            console.log('on left click');
                            $('#{$rightListErrorContainerId}').html('');
                            $('#{$rightListErrorContainerId}').hide();
                            var sourceOptions  = $('#{$rightListId} option').size();
                            var sourceSelected = $('#{$rightListId} option:selected').size();
                            var targetOptions  = $('#{$leftListId} option').size();
                            var targetSelected = $('#{$leftListId} option:selected').size();
                            var length = sourceOptions + targetOptions;
                            console.log(length);
                            return !$('#" . $rightListId . " option:selected')
                                .remove().appendTo('#" . $leftListId . "');
                        });";

        $script .= "$('body').on('click', '.compare-list-actions .fa-arrow-circle-up',function()
                        {
                            $('#{$rightListErrorContainerId}').html('');
                            $('#{$rightListErrorContainerId}').hide();
                            if ($('#" . $rightListId . " option:selected').first().index() > 0)
                            {
                                $('#" . $rightListId . " option:selected').each(function()
                                {
                                   $(this).insertBefore($(this).prev());
                                });
                            }
                        });
                   $('body').on('click', '.compare-list-actions .fa-arrow-circle-down',function()
                        {
                            $('#{$rightListErrorContainerId}').html('');
                            $('#{$rightListErrorContainerId}').hide();
                            if ($('#" . $rightListId . " option:selected').last().index() < ($('#" . $rightListId . " option').length - 1))
                            {
                                $($('#" . $rightListId . " option:selected').get().reverse()).map(function()
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
                                $('#" . $this->rightListId . " option').prop('selected', true);
                            });";
        $this->getView()->registerJs($script);
    }
}
?>