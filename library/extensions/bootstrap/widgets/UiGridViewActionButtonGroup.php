<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\widgets;

use usni\fontawesome\FA;
use usni\UsniAdaptor;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\components\UiHtml;
use usni\library\utils\BulkScriptUtil;
/**
 * UiGridViewActionButtonGroup class file. Consists of action buttons corresponding to actions that
 * can be performed on the grid.
 * 
 * @package usni\library\extensions\bootstrap\widgets
 */
class UiGridViewActionButtonGroup extends \yii\bootstrap\ButtonGroup
{
    /**
     * Toolbar to which button group is associated.
     * @var type
     */
    public $actionToolbar;

    /**
     * @var string the ID of the controller that should handle the actions specified here.
     * If not set, it will use the currently active controller. This property is mainly used by
     * [[urlCreator]] to create URLs for different actions. The value of this property will be prefixed
     * to each action name to form the route of the action.
     */
    public $controller;

    /**
     * Model associated to the grid view.
     * @var Model
     */
    public $model;

    /**
     * Grid view associated to the toolbar.
     * @var UiGridView
     */
    public $grid;
    
    /**
     * Overrides to set the buttons.
     * @return void
     */
    public function init()
    {
        parent::init();
        if($this->controller == null)
        {
            $this->controller = UsniAdaptor::app()->controller;
        }
        $this->setButtons();
    }

    /**
     * Set buttons.
     * @return void
     */
    protected function setButtons()
    {
        $buttons['create']      = $this->renderCreateButton();
        $buttons['settings']    = $this->renderSettingsFormLink();
        $buttons['bulkEdit']    = $this->renderBulkEditButton();
        $buttons['bulkDelete']  = $this->renderBulkDeleteButton();
        $this->buttons = $buttons;
    }

    /**
     * Renders create button
     * @return string
     */
    protected function renderCreateButton()
    {
        $label          = FA::icon('plus') . "\n" . UsniAdaptor::t('application', 'Create');
        $modelClassName = strtolower(UsniAdaptor::getObjectClassName($this->model));
        if($this->actionToolbar->showCreate && AuthManager::checkAccess(UsniAdaptor::app()->user->getUserModel(), $modelClassName . '.create'))
        {
            $createLink     = UiHtml::a($label, $this->getCreateUrl(), array('class' => 'btn btn-default', 'id' => 'action-toolbar-create'));
        }
        else
        {
            $createLink    = null;
        }
        return $createLink;
    }

    /**
     * Renders settings form.
     * @return string
     */
    protected function renderSettingsFormLink()
    {
        $optionsLink = null;
        if($this->actionToolbar->showSettings)
        {
            $optionsLabel   = FA::icon('cog') . "\n" . UsniAdaptor::t('settings', 'Settings');
            $optionsLink    = UiHtml::a($optionsLabel, '#', array(
                                                                  'data-toggle' => 'modal',
                                                                  'data-target' => '#gridSettings',
                                                                  'class'       => 'btn btn-default',
                                                                  'id'          => 'grid-settings-form'
                                                                ));
        }
        return $optionsLink;
    }

    /**
     * Render bulk edit button
     * @return string
     */
    protected function renderBulkEditButton()
    {
        $user           = UsniAdaptor::app()->user->getUserModel();
        $modelClassName = strtolower($this->model->formName());
        if($this->actionToolbar->showBulkEdit && AuthManager::checkAccess($user, $modelClassName . '.bulk-edit'))
        {
            $bulkEditlabel  = FA::icon('pencil') . "\n" . UsniAdaptor::t('application', 'Bulk Edit');
            $bulkUpdate     = UiHtml::a($bulkEditlabel,  '#', array(
                                        'class' => 'btn btn-default bulk-edit-btn', 'id' => 'action-toolbar-bulkedit'));
            return $bulkUpdate;
        }
    }

    /**
     * Render bulk delete button
     * @return string
     */
    protected function renderBulkDeleteButton()
    {
        $content         = null;
        $modelClassName  = strtolower($this->model->formName());
        $user            = UsniAdaptor::app()->user->getUserModel();
        if($this->actionToolbar->showBulkDelete && AuthManager::checkAccess($user, $modelClassName . '.bulk-delete'))
        {
            $label      = FA::icon('trash-o') . "\n" . UsniAdaptor::t('application', 'Bulk Delete');
            $content    = UiHtml::a($label, '#',
                                       ['class'         => 'btn btn-default multiple-delete', 'id' => 'action-toolbar-bulkdelete']);
            $pjaxId     = $this->getPjaxId();
            BulkScriptUtil::registerBulkDeleteScript($this->getBulkDeleteUrl(), 'grid-view', $this->getView(), $pjaxId);
        }
        return $content;
    }
    
    /**
     * Gets bulk delete url.
     * @return string
     */
    protected function getBulkDeleteUrl()
    {
        return UsniAdaptor::createUrl('/' . $this->getModuleId() . '/' . $this->controller->id . '/bulk-delete');
    }
    
    /**
     * Get Module Name.
     * @return string
     */
    protected function getModule()
    {
        return $this->controller->module->id;
    }
    
    /**
     * Gets create button url.
     * @return string
     */
    protected function getCreateUrl()
    {
        return UsniAdaptor::createUrl($this->getModuleId() . '/' . $this->controller->id . '/create');
    }

    /**
     * Get Module Id.
     * @return string
     */
    protected function getModuleId()
    {
        return $this->controller->module->getUniqueId();
    }
    
    /**
     * Get pjax id
     * @return string
     */
    public function getPjaxId()
    {
        $pjaxId = $this->actionToolbar->pjaxId;
        if($pjaxId == null)
        {
            $modelClass = UsniAdaptor::getObjectClassName($this->model);
            return strtolower($modelClass).'gridview-pjax';
        }
        return $pjaxId;
    }
}