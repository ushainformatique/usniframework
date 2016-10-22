<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\traits;

use usni\library\utils\ArrayUtil;
use usni\UsniAdaptor;
use usni\library\utils\StringUtil;
use usni\library\utils\MetadataUtil;

/**
 * GridViewTrait class file.
 * @package usni\library\traits
 */
trait GridViewTrait
{
    /**
     * Get default configuration for rendering grid view.
     * @return array
     */
    public function getDefaultGridConfiguration()
    {
        $modalDetailView    = true;
        $modelClassName     = $this->resolveModelClassName();
        $model              = new $modelClassName();
        $gridViewClassName  = $this->resolveGridViewClassName($model);
        $metaData           = MetadataUtil::getUserMetaDataForView(StringUtil::basename($gridViewClassName),
                                                                   UsniAdaptor::app()->user->getUserModel()->id);
        if(!empty($metaData))
        {
            $modalDetailView = ArrayUtil::getValue($metaData, 'modalDetailView', true);
        }
        $breadCrumbs        = $this->getGridViewBreadcrumb($model);
        return ['model'       => $model,
                'className'   => $gridViewClassName,
                'breadcrumbs' => $breadCrumbs,
                'controller'  => $this,
                'modalDetailView' => $modalDetailView];
    }

    /**
     * Get breadcrumb for grid view.
     * @param Model $model
     * @return array
     */
    public function getGridViewBreadcrumb($model)
    {
        $modelPluralLabel = $this->resolveModelPluralLabel($model);
        return [
            [
                'label' => UsniAdaptor::t('application', 'Manage') . ' ' . $modelPluralLabel
            ]
        ];
    }

    /**
     * Renders grid view.
     * @param array $config Configuration to create and render grid view.
     * The configuration consist of
     * - className Grid view class name
     * - breadcrumbs for grid view
     * - model Model associated with the grid view.
     * @return string
     */
    public function renderGridView($config = [])
    {
        $defaultGridConfiguration   = $this->getDefaultGridConfiguration();
        if(isset($config['breadcrumbs']))
        {
            ArrayUtil::popValue('breadcrumbs', $defaultGridConfiguration);
        }
        $configuration              = ArrayUtil::merge($defaultGridConfiguration, $config);
        $gridViewClass              = ArrayUtil::popValue('className', $configuration);
        $this->getView()->params['breadcrumbs'] = ArrayUtil::popValue('breadcrumbs', $configuration);
        $configuration['filterModel'] = $this->getFilterModel($configuration['model']);
        $gridView                     = new $gridViewClass($configuration);
        return $gridView->render();
    }

    /**
     * Gets filter model with search data if available.
     * @param Model $model
     * @return void
     */
    protected function getFilterModel($model)
    {
        //Get model class name without namespace.
        $searchModelClassName = $this->getSearchFormModelClassName($model);
        if ($searchModelClassName != null)
        {
            $baseSearchModelClassName = StringUtil::basename($searchModelClassName);
            $filterModel              = new $searchModelClassName();
            $filterModel->load($_GET, $baseSearchModelClassName);
            return $filterModel;
        }
        return null;
    }

    /**
     * Resolve grid view class name.
     * @param Model $model
     * @return string
     */
    protected function resolveGridViewClassName($model)
    {
        $modelClassName = get_class($model);
        $parts          = explode('models', $modelClassName);
        return $parts[0] . 'views' . $parts[1] . 'GridView';
    }

    /**
     * Get search form model class name.
     * @return string
     */
    protected function getSearchFormModelClassName($model)
    {
        $modelClassName = get_class($model);
        return $modelClassName . 'Search';
    }
}
?>
