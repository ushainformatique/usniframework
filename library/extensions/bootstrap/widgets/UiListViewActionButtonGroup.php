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
 * UiListViewActionButtonGroup class file. Consists of action buttons corresponding to actions that
 * can be performed on the list.
 * @package usni\library\extensions\bootstrap\widgets
 */
class UiListViewActionButtonGroup extends \yii\bootstrap\ButtonGroup
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
    public $list;
    
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
        $buttons['search']      = $this->renderSearchButton();
        $this->buttons = $buttons;
    }

    /**
     * Renders create button
     * @return string
     */
    protected function renderSearchButton()
    {
        $searchLink     = '';
        $label          = FA::icon('search') . "\n" . UsniAdaptor::t('application', 'Search');
        $modelClassName = strtolower(UsniAdaptor::getObjectClassName($this->model));
        if($this->actionToolbar->showSearch)
        {
            $searchLink     = UiHtml::a($label, '#', ['class' => 'btn btn-default search-button']);
        }
        else
        {
            $createLink    = null;
        }
        return $searchLink;
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
                                                                  'class'       => 'btn btn-default'
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
        if($this->actionToolbar->showBulkEdit && AuthManager::checkAccess($user, $modelClassName . '.bulkedit'))
        {
            $bulkEditlabel  = FA::icon('pencil') . "\n" . UsniAdaptor::t('application', 'Bulk Edit');
            $bulkUpdate     = UiHtml::a($bulkEditlabel,  '#', array(
                                        'class' => 'btn btn-default bulk-edit-btn'));
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
        $modelClassName  = strtolower(get_class($this->model));
        $user            = UsniAdaptor::app()->user->getUserModel();
        if($this->actionToolbar->showBulkDelete && AuthManager::checkAccess($user, $modelClassName . '.bulkdelete'))
        {
            $label      = FA::icon('trash-o') . "\n" . UsniAdaptor::t('application', 'Bulk Delete');
            $content    = UiHtml::a($label, '#',
                                       ['class'         => 'btn btn-default multiple-delete',]);
            $modelClass = UsniAdaptor::getObjectClassName($this->model);
            $pjaxId     = strtolower($modelClass).'gridview-pjax';
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
        return UsniAdaptor::createUrl('/' . $this->getModule() . '/' . $this->controller->id . '/bulk-delete');
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
        return $this->controller->module->id;
    }
}
?>