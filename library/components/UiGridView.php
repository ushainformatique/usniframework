<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\UsniAdaptor;
use usni\library\views\UiView;
use usni\library\utils\ArrayUtil;
use usni\library\extensions\bootstrap\widgets\UiGridViewActionToolBar;
use yii\data\ActiveDataProvider;
use yii\grid\CheckboxColumn;
use usni\library\extensions\bootstrap\widgets\UiGridViewWidget;
use yii\db\ActiveQuery;
use usni\library\utils\MetadataUtil;
use yii\widgets\Pjax;
use usni\library\extensions\bootstrap\widgets\UiLinkPager;
use Yii;
use yii\base\Model;
use yii\bootstrap\Modal;
use usni\library\utils\FlashUtil;

/**
 * Base class for rendering grid view. This renders the yii grid view widget.
 * 
 * @author Mayank Singhai <mayank.singhai@ushainformatique.com>
 * @package usni\library\components
 */
abstract class UiGridView extends UiView
{
    const DEFAULT_LIST_SIZE = 10;

    /**
     * DataProvider associated with the view.
     * @var ActiveDataProvider
     */
    public $dataProvider;

    /**
     * True/false to decide if each row in the list view widget will have a checkbox.
     * @var boolean
     */
    public $rowsAreSelectable = true;

    /**
     * Array of model ids. Each id is for a different row checked off.
     * @var array
     */
    protected $selectedIds;

    /**
     * Model associated with the grid view.
     * @var Model
     */
    public $model;

    /**
     * Params for the grid view.
     * @var array
     */
    public $params;

    /**
     * Filter model associated with the grid.
     * This model is populated with search data before being passed to this view.
     * @var Model
     */
    public $filterModel;

    /**
     * Pjax container id for the grid
     * @var string
     */
    public $pjaxContainerId;

    /**
     * Controller associated with the grid view.
     * @var Controller
     */
    public $controller;

    /**
     * If detail view would be modal.
     * @var boolean
     */
    public $modalDetailView = true;
    
    /**
     * Layout for the grid view
     * @var string 
     */
    public $layout;

    /**
     * Class constructor.
     *
     * @param array $config
     * @return void
     */
    public function __construct($config)
    {
        Yii::configure($this, $config);
        $this->pjaxContainerId = strtolower(UsniAdaptor::getObjectClassName($this)) . '-pjax';
    }

    /**
     * Renders content for a grid view.
     * @return string containing the element's content.
     */
    protected function renderContent()
    {
        //Load the grid view first so that available and displayed columns are populated.
        ob_start();
        Pjax::begin(['id' => $this->pjaxContainerId, 'enablePushState' => false, 'timeout' => 4000]);
        echo $this->renderGrid();
        Pjax::end();
        $output     = ob_get_clean();
        $content    = null;
        $content   .= $this->renderDeleteError();
        $content   .= $this->renderToolbar();
        $content   .= $output;
        $content   .= $this->renderDetailViewModal();
        return UiHtml::tag('div', $content, $this->getGridContainerOptions());
    }
    
    /**
     * Get grid container options
     * @return array
     */
    protected function getGridContainerOptions()
    {
        return ['class' => 'grid-container'];
    }


    /**
     * Renders delete error.
     * @return string
     */
    protected function renderDeleteError()
    {
        $isDeleteError = UsniAdaptor::app()->getSession()->hasFlash('deleteFailed');
        if($isDeleteError)
        {
            return FlashUtil::render('deleteFailed', 'alert alert-danger');
        }
        return null;
    }

    /**
     * Renders grid.
     * @return string
     */
    protected function renderGrid()
    {
        $widgetClassName    = $this->getGridViewWidgetPath();
        return $widgetClassName::widget($this->getGridViewParams());
    }

    /**
     * Gets grid view widget class name.
     * @return string
     */
    public function getGridViewWidgetPath()
    {
        return UiGridViewWidget::className();
    }

    /**
     * Get grid view params.
     * @return array
     */
    public function getGridViewParams()
    {
        return [
                    'id'                => $this->getId(),
                    'dataProvider'      => $this->getDataProvider(),
                    'caption'           => $this->renderTitle(),
                    'captionOptions'    => $this->getCaptionOptions(),
                    'options'           => $this->getOptions(),
                    'dataColumnClass'   => $this->getDataColumnClass(),
                    'columns'           => $this->resolveDisplayedColumnsInGridView(),
                    'showHeader'        => $this->shouldShowHeader(),
                    'pager'             => $this->getPager(),
                    'tableOptions'      => $this->getTableOptions(),
                    'summaryOptions'    => $this->getSummaryOptions(),
                    'summary'           => $this->getSummary(),
                    'owner'             => $this,
                    'layout'            => $this->getLayout(),
                    'filterModel'       => $this->filterModel,
                    'modalDetailView'   => $this->modalDetailView
               ];
    }

    /**
     * Get the meta data and merge with standard GridView column elements to create a column array that fits the GridView columns API.
     * @return string
     */
    public function getColumns()
    {
        return [];
    }

    /**
     * Get Grid view Id.
     * @return string
     */
    public function getId()
    {
        return 'grid-view';
    }

    /**
     * Get GridView First Column.
     * @return string
     */
    protected function getGridViewFirstColumn()
    {
        return ['class' => CheckboxColumn::className()];
    }

    /**
     * Get data provider for list view.
     * @return ActiveDataProvider
     */
    protected function getDataProvider()
    {
        if($this->dataProvider == null)
        {
            $gridViewClassName  = UsniAdaptor::getObjectClassName($this);
            $metadata           = MetadataUtil::getUserMetaDataForView($gridViewClassName, UsniAdaptor::app()->user->getUserModel()->id);
            if($this->filterModel != null && $this->filterModel instanceof Model)
            {
                $this->dataProvider = $this->filterModel->search();
                if(empty($this->dataProvider->sort))
                {
                    $this->dataProvider->sort = $this->resolveDataProviderSort();
                }
                $this->dataProvider->pagination = ['pageSize' => $this->resolvePageSize($metadata)];
            }
            else
            {
                $query                  = $this->resolveDataProviderQuery();
                $dataProviderClassName  = $this->getDataProviderClassName();
                $this->dataProvider     = new $dataProviderClassName(
                                                    [
                                                        'query'      => $query,
                                                        'sort'       => $this->resolveDataProviderSort(),
                                                        'pagination' => $this->getPagination($metadata),
                                                    ]
                );
            }
        }
        return $this->dataProvider;
    }
    
    /**
     * Get pagination
     * @param array $metadata
     * @return array
     */
    public function getPagination($metadata)
    {
        return ['pageSize' => $this->resolvePageSize($metadata)];
    }
    
    /**
     * Get data provider class name
     * @return string
     */
    public function getDataProviderClassName()
    {
        return ActiveDataProvider::className();
    }

    /**
     * Renders toolbar
     * @return array
     */
    protected function renderToolbar()
    {
        $options = [
                    'model'         => $this->model,
                    'searchModel'   => $this->getSearchModel(),
                    'searchViewClassName' => $this->getSearchViewClassName(),
                    'controller'    => $this->controller,
                    'options'       => ['class' => 'action-toolbar'],
                    'grid'          => $this
                   ];
        $options            = ArrayUtil::merge($options, static::getActionToolbarOptions());
        $gridViewToolbar    = static::getGridViewActionToolBarClassName();
        $content            = $gridViewToolbar::widget($options);
        return '<div class="block"><div class="well text-center">' . $content . '</div></div>';
    }

    /**
     * Checks whether grid view title should be rendered.
     * @return boolean
     */
    protected function shouldRenderTitle()
    {
        return true;
    }

    /**
     * To show or hide Header.
     * @return boolean
     */
    protected function shouldShowHeader()
    {
        return true;
    }

    /**
     * Get pager.
     * @return void
     */
    protected function getPager()
    {
        return ['class' => UiLinkPager::className()];
    }

    /**
     * Get items css class.
     * @return string
     */
    protected function getTableOptions()
    {
        return ['class' => 'table dataTable no-footer'];
    }

    /**
     * Get title.
     * @return string
     */
    protected function getTitle()
    {
        $title = null;
        if($this->shouldRenderTitle())
        {
            if(isset($this->params['title']))
            {
                $title = $this->params['title'];
            }
            if($this->model != null)
            {
                $title      = UsniAdaptor::t('application', 'Manage') . ' ' . $this->model->getLabel(2);
            }
        }
        return $title;
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
     * Get title.
     * @return string
     */
    public function renderTitle()
    {
        return $this->getTitle();
    }

    /**
     * Resolve columns that would be displayed in grid view.
     * @return array
     */
    protected function resolveDisplayedColumnsInGridView()
    {
        $columns             = array();
        if($this->renderCheckboxColumn())
        {
            $firstColumn = $this->getGridViewFirstColumn();
            array_push($columns, $firstColumn);
        }
        return ArrayUtil::merge($columns, $this->getColumns());
    }

    /**
     * Resolve data provider criteria
     * @return null
     */
    protected function resolveDataProviderQuery()
    {
        return new ActiveQuery(get_class($this->model));
    }

    /**
     * Resolve data provider sort
     * @return array
     */
    protected function resolveDataProviderSort()
    {
        return array();
    }

    /**
     * Resolve page size.
     * @param array $metadata
     * @return int
     */
    protected function resolvePageSize($metadata)
    {
        if(($pageSize = ArrayUtil::getValue($metadata, 'itemsPerPage')) == null)
        {
            $pageSize = self::DEFAULT_LIST_SIZE;
        }
        return $pageSize;
    }

    /**
     * Gets bulk delete url.
     * @return string
     */
    protected function getBulkDeleteUrl()
    {
        return UsniAdaptor::createUrl('/' . $this->getModule() . '/' . $this->controller->getId() . '/bulkDelete');
    }

    /**
     * Registers script.
     * @return void
     */
    protected function registerScripts()
    {
        parent::registerScripts();
        $script  = '$(function () {
                        $(\'body\').find(\'[rel="tooltip"]\').tooltip();
                      })';
        $this->getView()->registerJs($script, View::POS_END, 'tooltip');
    }

    /**
     * HTML attributes for the container tag of the grid view
     * @return array
     */
    protected function getOptions()
    {
        return ['class' => 'grid-view', 'id' => $this->getId()];
    }

    /**
     * Get default data column class.
     * @return string
     */
    protected function getDataColumnClass()
    {
        return 'usni\library\extensions\bootstrap\widgets\UiDataColumn';
    }

    /**
     * Get caption options.
     * @return string
     */
    protected function getCaptionOptions()
    {
        return ['class' => 'panel-title'];
    }

    /**
     * Get summary options.
     * @return array
     */
    protected function getSummaryOptions()
    {
        return ['class' => 'dataTables_info'];
    }

    /**
     * Get summary.
     * @return array
     */
    protected function getSummary()
    {
        return null;
    }

    /**
     * Get gridview action toolbar class name.
     * @return string
     */
    public static function getGridViewActionToolBarClassName()
    {
        return UiGridViewActionToolBar::className();
    }

    /**
     * Layout for the grid
     * @return string
     */
    protected function getLayout()
    {
        if($this->layout == null)
        {
            return "<div class='panel panel-default'>"
                        . "<div class='panel-heading'>{caption}</div>"
                        . "<div class='dataTable'>"
                            . "<div class='datatable-scroll'>{items}</div>"
                            . "<div class='datatable-footer'>{summary}{pager}</div>"
                        . "</div>"
                    . "</div>";
        }
        return $this->layout;
    }

    /**
     * Get search model
     * @return mixed Model|Null
     */
    protected function getSearchModel()
    {
        return null;
    }

    /**
     * Get search view class name.
     * @return UiView
     */
    protected function getSearchViewClassName()
    {
        return null;
    }

    /**
     * Get action toolbar options.
     * @return array
     */
    protected static function getActionToolbarOptions()
    {
        return ['showBulkEdit'            => true,
                'showBulkDelete'          => true,
                'showCreate'              => true,
                'showSettings'            => true];
    }

    /**
     * Gets filter model unqualified name.
     * @return mixed
     */
    protected function getFilterModelClass()
    {
        if($this->filterModel instanceof Model)
        {
            return UsniAdaptor::getObjectClassName($this->filterModel);
        }
        return null;
    }

    /**
     * Get default filter options.
     * @return array
     */
    protected function getDefaultFilterOptions()
    {
        return ['class' => 'form-control', 'id' => null];
    }

    /**
     * Renders detailview modal.
     * @return string
     */
    protected function renderDetailViewModal()
    {
        $viewFile = UsniAdaptor::getAlias('@usni/themes/bootstrap/views/site/_modalview') . '.php';
        $output   = $this->getView()->renderPhpFile($viewFile, ['modalId' => 'gridContentModal',
                                                    'size'    => Modal::SIZE_LARGE,
                                                    'title'   => UsniAdaptor::t('yii', 'View') . 
                                                                 ' ' . UsniAdaptor::getObjectClassName($this->model) . ' ' . UsniAdaptor::t('application', 'Detail'),
                                                    'body'    => null,
                                                    'footer'  => null]);
        $this->registerModalDetailScript();
        return $output;
    }

    /**
     * Get detail view modal options.
     * @return array
     */
    protected function getDetailViewModalOptions()
    {
        return ['size' => Modal::SIZE_LARGE];
    }

    /**
     * Registers script.
     * @return void
     */
    protected function registerModalDetailScript()
    {
        $script     = "$('#gridContentModal').on('show.bs.modal', function (event) {
                       var button = $(event.relatedTarget) // Button that triggered the modal
                       var url = button.data('url') // Extract info from data-* attributes
                       $(this).find('.modal-body').load(url);
                      })";
        $this->getView()->registerJs($script);
    }

    /**
     * Should checkbox column be rendered.
     * @return boolean
     */
    protected function renderCheckboxColumn()
    {
        return $this->rowsAreSelectable;
    }
}