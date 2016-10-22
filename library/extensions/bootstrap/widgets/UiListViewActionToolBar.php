<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\widgets;

use usni\library\components\UiHtml;
use yii\bootstrap\Widget;
use usni\library\extensions\bootstrap\widgets\UiListViewActionButtonGroup;
use usni\UsniAdaptor;

/**
 * ButtonToolbar renders a button toolbar bootstrap component.
 *
 * For example,
 *
 * ```php
 * // a button toolbar group with items configuration
 * echo UiListViewActionToolBar::widget([
 *     'model'         => $this->model,
 *     'controller'    => $this->getController(),
 *     'options'       => ['class' => 'action-toolbar'],
 *     'grid'          => $this
 * ]);
 * ```
 * @see usni\library\components\UiListView
 * @see http://getbootstrap.com/components/#btn-groups-toolbar
 * @see http://getbootstrap.com/components/#btn-groups
 */
class UiListViewActionToolBar extends Widget
{
    /**
     * List view associated to the toolbar.
     * @var UiListView
     */
    public $list;

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
     * Show search button.
     * @var boolean
     */
    public $showSearch = true;

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
        $listViewActionButtonGroup = static::getListViewActionButtonGroup();
        return $listViewActionButtonGroup::widget($options);
    }

    /**
     * Get list view action button group.
     * @return string
     */
    protected static function getListViewActionButtonGroup()
    {
        return UiListViewActionButtonGroup::className();
    }

    /**
     * Renders toolbar content.
     * @return type
     */
    protected function renderToolbarContent()
    {
//        $content    = null;
//        $content   .= $this->renderSettings();
//        $content   .= $this->renderBulkEditForm();
//        return $content;
        return '';
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
            $view                    = new UiGridSettingsView($model, $this->grid);
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
                //$bulkEditView   = new $bulkEditViewClassName($this->model, $this->getId());
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
}
