<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\library\views\UiView;
use usni\UsniAdaptor;
use usni\library\extensions\bootstrap\widgets\UiDetailViewWidget;
use usni\library\utils\ArrayUtil;
use usni\library\components\UiHtml;
use usni\library\extensions\bootstrap\widgets\UiHeading;
use usni\fontawesome\FA;
use usni\library\modules\auth\managers\AuthManager;
use yii\bootstrap\ButtonDropdown;
use usni\library\views\UiBrowseModelView;
use Yii;
use usni\library\utils\MetadataUtil;
use usni\library\utils\DateTimeUtil;
use usni\library\modules\users\utils\UserUtil;
use yii\base\InvalidValueException;

/**
 * Abstract base class to render the details.
 * @package usni\library\views
 */
abstract class UiDetailView extends UiView
{
    /**
     * Controller associated with the detail view.
     * @var Controller
     */
    public $controller;

    /**
     * Model assocaited to the detail view.
     * @var Model
     */
    public $model;

    /**
     * Params for the detail view.
     * @var array
     */
    public $params;

    /**
     * Class constructor.
     * @param array $config
     * @return void
     */
    public function __construct($config)
    {
        Yii::configure($this, $config);
    }

    /**
     * Get columns data.
     * @return void
     */
    abstract public function getColumns();

    /**
     * Override to wrap in a container.
     * @return string
     */
    public function render()
    {
        $content = null;
        //If view is displayed as modal than don't render browse model dropdown
        $viewMetadata = MetadataUtil::getUserMetaDataForView($this->resolveGridViewClassName(), UsniAdaptor::app()->user->getUserModel()->id);
        if(((bool)ArrayUtil::getValue($viewMetadata, 'modalDetailView')) === false)
        {
            $content  .= $this->renderDetailModelBrowseView();
        }
        $parentContent = parent::render();
        $parentContent = $this->wrapView($parentContent);
        return $content . $parentContent;
    }

    /**
     * Renders content for a detail view.
     * @return string containing the element's content.
     */
    protected function renderContent()
    {
        $content = null;
        $columns = $this->getColumns();
        if (!empty($columns))
        {
            $widgetClassName = $this->getWidgetPath();
            $content    .= $widgetClassName::widget($this->getDetailViewParams());
        }
        return $content;
    }

    /**
     * Gets detail view params.
     * @return array
     */
    protected function getDetailViewParams()
    {
        return array(
            'model'             => $this->model,
            'template'          => $this->getItemTemplate(),
            'options'           => $this->getHtmlOptions(),
            'attributes'        => $this->resolveColumnsToBeDisplayed()
        );
    }

    /**
     * Gets widget path.
     * @return string
     */
    protected function getWidgetPath()
    {
        return UiDetailViewWidget::className();
    }

    /**
     * Gets item template.
     * @return string
     */
    protected function getItemTemplate()
    {
        return "<tr><th>{label}</th><td>{value}</td></tr>\n";
    }

    /**
     * Gets html options.
     * @return boolean
     */
    protected function getHtmlOptions()
    {
        return ['class' => 'table table-striped table-detail detail-view'];
    }

    /**
     * Get detail view title.
     * @return string
     */
    protected function getTitle()
    {
        return null;
    }

    /**
     * Renders title.
     * @return string
     */
    protected function renderTitle()
    {
        $small      = UiHeading::widget(['content' => $this->getSecondaryTitle(),
                                         'options' => $this->getSecondaryHtmlOptions(),
                                         'tag'     => 'small']);
        $title      = UiHeading::widget(['content' => $this->getIcon() . "\n" . $this->getTitle() . $small,
                                         'options' => $this->getTitleHtmlOptions(),
                                         'tag'     => $this->getTitleTag()]);
        $options    = $this->renderOptions();
        $content    = UiHtml::tag('div', $title . $options, ['class' => 'panel-heading']);
        return $content;
    }
    
    /**
     * Get icon
     * @return string
     */
    protected function getIcon()
    {
        return FA::icon('book');
    }


    /**
     * Resolve grid view class name on which detail is being displayed.
     * @return string
     */
    protected function resolveGridViewClassName()
    {
        $detailViewClassName = UsniAdaptor::getObjectClassName($this);
        return str_replace('DetailView', '', $detailViewClassName) . 'GridView';
    }

    /**
     * This method is invoked at the beginning of {@link renderContent()}.
     * @param string &$content The content to be rendered.
     * @return boolean whether the view should be rendered.
     * @since 1.1.5
     */
    protected function beforeRender(&$content)
    {
        if($this->shouldRenderTitle())
        {
            $content = $this->renderTitle();
        }
        return true;
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
     * Check if created attributes be displayed.
     * @return boolean
     */
    protected static function shouldRenderCreatedAttributes()
    {
        return true;
    }

    /**
     * Check if modified attributes be displayed.
     * @return boolean
     */
    protected static function shouldRenderModifiedAttributes()
    {
        return true;
    }

    /**
     * Resolve columns to be displayed in detail view.
     * @return array
     */
    protected function resolveColumnsToBeDisplayed()
    {
        $columns = $this->getColumns();
        if (static::shouldRenderCreatedAttributes())
        {
            $columns = ArrayUtil::merge($columns, [
                    ['attribute' => 'created_by',       'value' => $this->resolveCreatedBy()],
                    ['attribute' => 'created_datetime', 'value' => $this->resolveDateTimeAttribute('created_datetime')]
            ]);
        }
        if (static::shouldRenderModifiedAttributes())
        {
            $columns = ArrayUtil::merge($columns, [
                    ['attribute' => 'modified_by',       'value' => $this->resolveModifiedBy()],
                    ['attribute' => 'modified_datetime', 'value' => $this->resolveDateTimeAttribute('modified_datetime')]
            ]);
        }
        return $columns;
    }
    
    /**
     * Resolve date time attribute
     * @param string $attribute
     * @return string
     */
    protected function resolveDateTimeAttribute($attribute)
    {
        return DateTimeUtil::getFormattedDateTime($this->model[$attribute]);
    }


    /**
     * Resolve created by
     * @return string
     */
    protected function resolveCreatedBy()
    {
        return UserUtil::getRecordEditorName($this->model['created_by']);
    }
    
    /**
     * Resolve modified by
     * @return string
     */
    protected function resolveModifiedBy()
    {
        return UserUtil::getRecordEditorName($this->model['modified_by']);
    }

    /**
     * Gets delete button url.
     *
     * @return string
     */
    protected function getDeleteUrl()
    {
        return UsniAdaptor::createUrl('/' . $this->getModule() . '/' . $this->controller->id . '/delete', ['id' => $this->model['id']]);
    }

    /**
     * Gets edit button url.
     *
     * @return string
     */
    protected function getEditUrl()
    {
        return UsniAdaptor::createUrl('/' . $this->getModule() . '/' . $this->controller->id . '/update', ['id' => $this->model['id']]);
    }

    /**
     * Should title be rendered
     * @return boolean
     */
    protected function shouldRenderTitle()
    {
        return true;
    }

    /**
     * Wraps view.
     * @param string $content
     * @return string
     */
    protected function wrapView($content)
    {
        return UiHtml::tag('div', $content, ['class' => 'panel panel-default detail-container']);
    }

    /**
     * Gets title tag.
     * @return string
     */
    protected function getTitleTag()
    {
        return 'h6';
    }

    /**
     * Gets title html options.
     * @return array
     */
    protected function getTitleHtmlOptions()
    {
        return ['class' => 'panel-title'];
    }

    /**
     * Gets secondary title.
     * @return string
     */
    protected function getSecondaryTitle()
    {
        return null;
    }

    /**
     * Gets secondary html options.
     * @return array
     */
    protected function getSecondaryHtmlOptions()
    {
        return array();
    }

    /**
     * Render options for detail view.
     * @return string
     */
    protected function renderOptions()
    {
        $optionItems = $this->getOptionItems();
        if($optionItems != null)
        {
            return ButtonDropdown::widget([
                                            'label'             => UsniAdaptor::t('application', 'Options'),
                                            'containerOptions'  => ['class' => 'pull-right'],
                                            'options'           => ['class' => 'btn-warning btn-sm'],
                                            'dropdown'          => ['items' => $optionItems, 'encodeLabels' => false, 'options' => ['class' => 'dropdown-menu-right']],
                                            'encodeLabel'       => false,
                                        ]);
        }
        else
        {
            return null;
        }
    }

    /**
     * Get option items.
     * @return array
     */
    protected function getOptionItems()
    {
        $user       = UsniAdaptor::app()->user->getUserModel();
        $editLink   = null;
        $deleteLink = null;
        $modelPermissionName = $this->getPermissionPrefix();
        if($modelPermissionName == null)
        {
            throw new InvalidValueException(UsniAdaptor::t('application', 'The permission prefix can not be null'));
        }
        $editLabel          = FA::icon('pencil') . "\n" . UsniAdaptor::t('application', 'Edit');
        $deleteLabel        = FA::icon('trash-o') . "\n" . UsniAdaptor::t('application', 'Delete');
        if($user->id != $this->model['created_by'])
        {
            if(AuthManager::checkAccess($user, $modelPermissionName . '.updateother'))
            {
                $editLink   = $this->getEditUrl();
            }
            if(AuthManager::checkAccess($user, $modelPermissionName . '.deleteother'))
            {
                $deleteLink = $this->getDeleteUrl();
            }
        }
        else
        {
            if(AuthManager::checkAccess($user, $modelPermissionName . '.update'))
            {
                $editLink   = $this->getEditUrl();
            }
            if(AuthManager::checkAccess($user, $modelPermissionName . '.delete'))
            {
                $deleteLink = $this->getDeleteUrl();
            }
        }
        $linkArray = array();
        if($editLink != null)
        {
            $linkArray[] = ['url' => $editLink, 'label' => $editLabel];
        }
        if($deleteLink != null)
        {
            $linkArray[] = ['url' => $deleteLink, 'label' => $deleteLabel];
        }
        return $linkArray;
    }

    /**
     * Get model dropdown list.
     * @return string
     */
    protected function renderDetailModelBrowseView()
    {
        $viewClassName = static::resolveBrowseModelViewClassName();
        if($viewClassName != null)
        {
            $view          = new $viewClassName(
                                                [
                                                    'model' => $this->model, 
                                                    'attribute' => $this->resolveDefaultBrowseByAttribute(), 
                                                    'shouldRenderOwnerCreatedModelsForBrowse' => $this->shouldRenderOwnerCreatedModels()
                                                ]
                                                );
            return $view->render();
        }
        return null;
    }

    /**
     * Resolve default browse by attribute.
     * @return string
     */
    protected function resolveDefaultBrowseByAttribute()
    {
        return 'name';
    }

    /**
     * Should render owner created models for browse only. Thus if permission for view others
     * is true, this is false because user can see all the models. If only view permission is there
     * than this is true as owner wants to see all his post.
     * @return boolean
     */
    protected function shouldRenderOwnerCreatedModels()
    {
        $user = UsniAdaptor::app()->user->getUserModel();
        $lowerModelClassName = $this->getPermissionPrefix();
        if(AuthManager::checkAccess($user, $lowerModelClassName . '.viewother'))
        {
            return false;
        }
        return true;
    }

    /**
     * Resolve browse model view class name.
     * @return string
     */
    protected static function resolveBrowseModelViewClassName()
    {
        return UiBrowseModelView::className();
    }
    
    /**
     * Get permission prefix
     * @return string
     */
    protected function getPermissionPrefix()
    {
        if($this->model instanceof \yii\db\ActiveRecord)
        {
            return strtolower(UsniAdaptor::getObjectClassName($this->model));
        }
        return null;
    }
}
?>