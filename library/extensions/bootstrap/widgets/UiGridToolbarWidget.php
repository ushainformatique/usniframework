<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\widgets;

use usni\UsniAdaptor;
use yii\bootstrap\Button;
use usni\library\components\UiHtml;

/**
 * UiGridToolbarWidget class file.
 * @package usni\library\extensions\bootstrap\widgets
 */
class UiGridToolbarWidget extends \yii\bootstrap\Widget
{
    /**
     * Flag to check if help is to be displayed or not.
     * @var boolean
     */
    public $showHelp = false;
    /**
     * Model asociated with the grid view
     * @var Model
     */
    public $model;
    /**
     * Template for the help widget.
     * @var string
     */
    public $template = '{content}';

    /**
     * Runs the widget.
     */
    public function run()
    {
        if($this->showHelp)
        {
            $entityId = $this->getHelpEntityId();
            if($entityId != null)
            {
                $content = HelpManager::getContent($entityId);
                if(!empty($content))
                {
                    $closeButton = Button::widget(['label' => '×',
                                                   'options' => ['class' => 'close', 'data-dismiss' => 'alert']
                                                  ]);
                    $template    = UiHtml::tag('div', $closeButton . $this->template, $this->options);
                    echo str_replace('{content}', $content, $template);
                }
            }
        }
    }

    /**
     * Gets help entity id.
     * @return null
     */
    protected function getHelpEntityId()
    {
        $modelClassName = UsniAdaptor::getObjectClassName($this->model);
        return $modelClassName . '.manage';
    }
}
?>