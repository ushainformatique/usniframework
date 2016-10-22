<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\widgets;

use usni\library\components\UiHtml;
use yii\bootstrap\Widget;
use usni\library\extensions\bootstrap\widgets\UiGridViewActionButtonGroup;
use usni\UsniAdaptor;
use usni\library\utils\MetadataUtil;
use usni\library\components\UiGridView;
use usni\library\models\GridViewOptionsForm;
use usni\library\views\UiGridSettingsView;

/**
 * ButtonToolbar renders a button toolbar bootstrap component.
 *
 * For example,
 *
 * ```php
 * // a button toolbar group with items configuration
 * echo UiGridViewActionToolBar::widget([
 *     'model'         => $this->model,
 *     'controller'    => $this->getController(),
 *     'options'       => ['class' => 'action-toolbar'],
 *     'grid'          => $this
 * ]);
 * ```
 * @see usni\library\components\UiGridView
 * @see http://getbootstrap.com/components/#btn-groups-toolbar
 * @see http://getbootstrap.com/components/#btn-groups
 */
class UiGridViewActionToolBar extends Widget
{
    /**
     * Grid view associated to the toolbar.
     * @var UiGridView
     */
    public $grid;

    /**
     * @var string the ID of the controller that should handle the actions specified here.
     * If not set, it will use the currently active controller.
     */
    public $controller;

    /**
     * Model associated to the grid view.
     * @var Model
     */
    public $model;

    /**
     * @var array list of button groups. Each array element represents a button group
     */
    public $buttonGroups = [];

    /**
     * Show settings button.
     * @var boolean
     */
    public $showSettings = true;

    /**
     * Show bulk edit button.
     * @var boolean
     */
    public $showBulkEdit = false;

    /**
     * Show bulk delete button.
     * @var boolean
     */
    public $showBulkDelete = false;

    /**
     * Show create button.
     * @var boolean
     */
    public $showCreate = true;

    /**
     * Search Model for the grid view.
     * @var mixed Model|null
     */
    public $searchModel;

    /**
     * Search view class name.
     * @var UiView
     */
    public $searchViewClassName;
    
    /**
     * Grid view action button group class
     * @var UiGridViewActionButtonGroup 
     */
    public $gridViewActionButtonGroup;
    
    /**
     * Pjax id for the grid view
     * @var string
     */
    public $pjaxId;

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();
        if($this->controller == null)
        {
            $this->controller = UsniAdaptor::app()->controller;
        }
        UiHtml::addCssClass($this->options, 'btn-toolbar');
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $content = $this->renderButtonGroup();
        $content = UiHtml::tag('div', $content, $this->options);
        echo $content;
        $toolbarContent = UiHtml::tag('div', $this->renderToolbarContent(), ['id' => 'toolbar-content']);
        echo $toolbarContent;
    }

    /**
     * Renders button group.
     * @return string the rendering result.
     */
    protected function renderButtonGroup()
    {
        $options                   = ['model'       => $this->model,
                                      'controller'  => $this->controller,
                                      'actionToolbar' => $this];
        $gridViewActionButtonGroup = $this->getGridViewActionButtonGroup();
        return $gridViewActionButtonGroup::widget($options);
    }

    /**
     * Get grid view action button group.
     * @return string
     */
    public function getGridViewActionButtonGroup()
    {
        if($this->gridViewActionButtonGroup == null)
        {
            return UiGridViewActionButtonGroup::className();
        }
        return $this->gridViewActionButtonGroup;
    }

    /**
     * Renders toolbar content.
     * @return type
     */
    protected function renderToolbarContent()
    {
        $content    = null;
        $content   .= $this->renderSettings();
        $content   .= $this->renderBulkEditForm();
        return $content;
    }

    /**
     * Render screen options.
     * @return string
     */
    protected function renderSettings()
    {
        if($this->showSettings)
        {
            $model                   = new GridViewOptionsForm();
            $gridViewClassName       = UsniAdaptor::getObjectClassName($this->grid);
            $userId                  = UsniAdaptor::app()->user->getUserModel()->id;
            $data                    = MetadataUtil::getUserMetaDataForView($gridViewClassName, $userId);
            $model->setAttributes($data);
            $gridSettingViewClassName   = $this->getGridSettingViewClassName();
            $view                       = new $gridSettingViewClassName($model, $this->grid);
            return $view->render();
        }
        return null;
    }

    /**
     * Renders search form
     * @return string
     */
    protected function renderBulkEditForm()
    {
        if($this->showBulkEdit)
        {
            $bulkEditViewClassName    = $this->getBulkEditViewClassName();
            $formContent              = null;
            if (@class_exists($bulkEditViewClassName))
            {
                $bulkEditView   = new $bulkEditViewClassName($this->model, $this->grid->id, $this->grid->pjaxContainerId);
                $formContent    = UiHtml::tag('div', $bulkEditView->render(), ['class' => 'bulk-edit-form', 'style' => 'display:none']);
            }
            return $formContent;
        }
        return null;
    }

    /**
     * Gets bulk edit view class name.
     * @return string
     */
    protected function getBulkEditViewClassName()
    {
            $modelClassName = get_class($this->model);
            $parts          = explode('models', $modelClassName);
            return $parts[0] . 'views' . $parts[1] . 'BulkEditView';
    }
    
    /**
     * Gets grid settings view class name.
     * @return string
     */
    protected function getGridSettingViewClassName()
    {
        return UiGridSettingsView::className();
    }
}
