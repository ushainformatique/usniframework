<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\library\views\UiView;
use usni\UsniAdaptor;
use usni\library\extensions\bootstrap\widgets\UiListViewActionToolBar;
use Yii;
use usni\library\components\UiHtml;
use usni\library\widgets\UiListViewWidget;
use usni\library\extensions\bootstrap\widgets\UiLinkPager;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;
use usni\library\extensions\bootstrap\widgets\UiBadge;
use yii\bootstrap\Button;
use usni\library\utils\ArrayUtil;
use usni\library\utils\MetadataUtil;
/**
 * UiListView class file
 * 
 * @package usni\library\components
 */
abstract class UiListView extends UiView
{
    const DEFAULT_LIST_SIZE = 10;
    
    /**
     * Data provider for the list view.
     * @var DataProvider
     */
    public $dataProvider;

    /**
     * Unique identifier of the list view widget. Allows for multiple list view widgets on a single page.
     * @var string
     */
    protected $listId;

    /**
     * Model assocaited to the list view.
     * @var Model
     */
    public $model;
    
    /**
     * Params for the list view.
     * @var array
     */
    public $params;
    
    /**
     * Pjax container id for the grid
     * @var string
     */
    public $pjaxContainerId;

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
     * Renders content for a list view.
     * @return string containing the element's content.
     */
    protected function renderContent()
    {
        ob_start();
        Pjax::begin(['id' => $this->pjaxContainerId, 'enablePushState' => false, 'timeout' => 2000]);
        echo $this->renderList();
        Pjax::end();
        $output     = ob_get_clean();
        $content    = $this->renderToolbar();
        $content   .= $this->renderSearchForm();
        $content   .= $this->renderSortBy();
        $content   .= $output;
        return UiHtml::tag('div', $content, ['class' => 'list-container']);
    }

    /**
     * Renders list.
     * @return string
     */
    protected function renderList()
    {
        $widgetClassName    = $this->getListViewWidgetPath();
        return $widgetClassName::widget($this->getListViewParams());
    }
    
    /**
     * Gets list view widget.
     * @return string
     */
    protected function getListViewWidgetPath()
    {
        return UiListViewWidget::className();
    }

    /**
     * Get list view params.
     * @return array
     */
    protected function getListViewParams()
    {
        $params = [
                        'id'                => $this->getListViewId(),
                        'dataProvider'      => $this->getDataProvider(),
                        'caption'           => $this->renderTitle(),
                        'captionOptions'    => $this->getCaptionOptions(),
                        'options'           => $this->getOptions(),
                        'pager'             => $this->getPager(),
                        'summaryOptions'    => $this->getSummaryOptions(),
                        'summary'           => $this->getSummary(),
                        'owner'             => $this,
                        'layout'            => $this->getLayout(),
                        'itemView'          => $this->getItemView(),
                        'emptyText'         => $this->getEmptyText(),
                        'emptyTextOptions'  => $this->getEmptyTextOptions(),
                        'itemOptions'       => $this->getItemOptions(),
                        'viewParams'        => $this->getViewParams()
                  ];
        return $params;
    }

    /**
     * Get view used to render each item.
     * @return string
     */
    abstract protected function getItemView();

    /**
     * Gets list view id.
     * @return string
     */
    protected function getListViewId()
    {
        return $this->listId;
    }

    /**
     * Get data provider for list view.
     * @return ActiveDataProvider
     */
    protected function getDataProvider()
    {
        if($this->dataProvider == null)
        {
            $metadata           = [];
            $listViewClassName  = UsniAdaptor::getObjectClassName($this);
            if(!UsniAdaptor::app()->user->isGuest)
            {
                $metadata = MetadataUtil::getUserMetaDataForView($listViewClassName, UsniAdaptor::app()->user->getUserModel()->id);
            }
            $query              = $this->resolveDataProviderQuery();
            $dataProviderClassName  = $this->getDataProviderClassName();
            $this->dataProvider     = new $dataProviderClassName(
                                                    [
                                                        'query'      => $query,
                                                        'sort'       => $this->resolveDataProviderSort(),
                                                        'pagination' => $this->getPagination($metadata),
                                                    ]
            );   
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
     * Gets title.
     * @return string
     */
    protected function renderTitle()
    {
        return $this->getTitle();
    }

    /**
     * Gets title.
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
                $title = UsniAdaptor::t('application', 'List') . ' ' . $this->model->getLabel(2);
            }
        }
        return $title;
    }

    /**
     * Checks whether list view title should be rendered.
     * @return boolean
     */
    protected function shouldRenderTitle()
    {
        return true;
    }

    /**
     * Gets the empty text.
     * @return string
     */
    protected function getEmptyText()
    {
        return UsniAdaptor::t('application', 'No Data Available');
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
     * Renders toolbar.
     * @return array
     */
    public function renderToolbar()
    {
        if($this->shouldRenderToolBar())
        {
            $options = [
                           'model'        => $this->model,
                           'options'      => [],
                       ];
            $options         = ArrayUtil::merge($options, static::getActionToolbarOptions());
            $listViewToolbar = self::getListViewActionToolBarClassName();
            return $listViewToolbar::widget($options);
            
        }
        return null;
    }

    /**
     * Should render search form.
     * @return boolean
     */
    protected function shouldRenderSearchForm()
    {
        return false;
    }

    /**
     * Renders search form
     * @return type
     */
    protected function renderSearchForm()
    {
        if($this->shouldRenderSearchForm())
        {
            $searchViewClassName    = $this->getSearchViewClassName();
            $formContent            = null;
            if (@class_exists($searchViewClassName))
            {
                $searchView = new $searchViewClassName($this->model, $this->getListViewId());
                $formContent = UiHtml::tag('div', $searchView->render(), ['class' => 'search-form', 'style' => 'display:none']);
            }
            return $formContent;
        }
        return null;
    }

    /**
     * Gets search view class name.
     * @return string
     */
    protected function getSearchViewClassName()
    {
        $modelClass = get_class($this->model);
        return $modelClass . 'SearchView';
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
     * Get sortable attributes.
     * @return string
     */
    protected function renderSortBy()
    {
        return null;
    }

    /**
     * Registers script.
     * @return void
     */
    protected function registerScripts()
    {
        parent::registerScripts();
        $script  = '$(function () {
                        $(\'[rel="tooltip"]\').tooltip()
                      })';
        $this->getView()->registerJs($script);
    }
    
    /**
     * Get listview action toolbar class name.
     * @return string
     */
    protected static function getListViewActionToolBarClassName()
    {
        return UiListViewActionToolBar::className();
    }
    
    /**
     * Get caption options.
     * @return string
     */
    protected function getCaptionOptions()
    {
        return ['class' => 'page-title'];
    }
    
    /**
     * HTML attributes for the container tag of the grid view
     * @return array
     */
    protected function getOptions()
    {
        return ['class' => 'list-view', 'id' => $this->getListViewId()];
    }
    
    /**
     * Get options for each item in the list
     * @return array
     */
    protected function getItemOptions()
    {
        return [];
    }
    
    /**
     * Get summary options.
     * @return array
     */
    protected function getSummaryOptions()
    {
        return ['class' => 'list-summary'];
    }

    /**
     * Get summary.
     * @return string
     */
    protected function getSummary()
    {
        return null;
        $count   = $this->dataProvider->getTotalCount();
        $count   = UiBadge::widget(['content' => $count]);
        $results = Button::widget([
                                   'options' => ['class' => 'btn-warning btn-xs'],
                                   'label'   => UsniAdaptor::t('application', 'Results {count}', ['count' => $count]),
                                   'encodeLabel' => false
                                  ]);
        return UiHtml::tag('div', $results, ['class' => 'grid-summary']);
    }
    
    /**
     * Layout for the list
     * @return string
     */
    protected function getLayout()
    {
        return "{caption}\n<div class='panel panel-content'>{summary}\n{items}\n{pager}</div>";
    }
    
    /**
     * Should render tool bar.
     * @return boolean
     */
    protected function shouldRenderToolBar()
    {
        return true;
    }
    
    /**
     * Get action toolbar options.
     * @return array
     */
    protected static function getActionToolbarOptions()
    {
        return ['showSearch' => false];
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
     * @var array additional parameters to be passed to [[itemView]] when it is being rendered.
     * This property is used only when [[itemView]] is a string representing a view name.
     */
    protected function getViewParams()
    {
        return [];
    }
    
    /**
     * Get empty text options
     * @return array
     */
    public function getEmptyTextOptions()
    {
        return ['class' => 'empty'];
    }
}