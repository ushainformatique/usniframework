<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\traits;

use usni\library\utils\ArrayUtil;
use usni\UsniAdaptor;
use usni\library\utils\PermissionUtil;
use yii\db\ActiveRecord;
use yii\base\InvalidValueException;

/**
 * DetailViewTrait class file.
 * @package usni\library\traits
 */
trait DetailViewTrait
{
    /**
     * Get default configuration for rendering detail view.
     * @param int $id
     * @return array
     */
    public function getDefaultDetailConfiguration($id)
    {
        $modelClassName      = $this->resolveModelClassName();
        $model               = $this->loadModel($modelClassName, $id);
        $detailViewClassName = $this->resolveDetailViewClassName($model);
        $breadCrumbs         = $this->getDetailViewBreadcrumb($model);
        return ['model'     => $model,
                'className' => $detailViewClassName,
                'breadcrumbs' => $breadCrumbs,
                'controller'  => $this];
    }

    /**
     * Get breadcrumb for detail view.
     * @param Model $model
     * @return array
     */
    public function getDetailViewBreadcrumb($model)
    {
        $modelPluralLabel = $this->resolveModelPluralLabel($model);
        return [
                    [
                        'label' => UsniAdaptor::t('application', 'Manage') . ' ' . $modelPluralLabel,
                        'url'   => $this->getBreadCrumbManageUrl()
                    ],
                    [
                        'label' => UsniAdaptor::t('application', 'View') . ' #' . UsniAdaptor::getRequestParam('id')
                    ]
                ];
    }

    /**
     * Renders detail view.
     * @param int $id
     * @param array $config Configuration to create and render detail view.
     * The configuration consist of
     * - className Detail view class name
     * - breadcrumbs for detail view
     * - model Model associated with the detail view.
     * - isArrayRecord if true than everything has to be passed in config
     * @return string
     */
    public function renderDetailView($id, $config = [])
    {
        $user                       = UsniAdaptor::app()->user->getUserModel();
        $isArray                    = ArrayUtil::getValue($config, 'isArrayRecord');
        if($isArray !== true)
        {
            $defaultDetailConfiguration   = $this->getDefaultDetailConfiguration($id);
            $configuration              = ArrayUtil::merge($defaultDetailConfiguration, $config);
        }
        else
        {
            $configuration              = $config;
        }
        $detailViewClass            = ArrayUtil::popValue('className', $configuration);
        $this->getView()->params['breadcrumbs'] = ArrayUtil::popValue('breadcrumbs', $configuration);
        $module                     = UsniAdaptor::app()->getModule($this->module->id);
        if(method_exists($module, 'getPermissionUtil'))
        {
            $permissionUtil = $module->getPermissionUtil();
        }
        else
        {
            $permissionUtil = PermissionUtil::className();
        }
        $permissionPrefix      = $this->getPermissionPrefix($configuration['model']);
        if($permissionPrefix == null)
        {
            throw new InvalidValueException(UsniAdaptor::t('application', 'The permission prefix can not be null'));
        }
        $isPermissible         = $permissionUtil::doesUserHavePermissionToPerformAction($configuration['model'], $user, $permissionPrefix . '.viewother');
        if(!$isPermissible)
        {
            throw new \yii\web\ForbiddenHttpException(\Yii::t('yii','You are not authorized to perform this action.'));
        }
        else
        {
            $detailView                 = new $detailViewClass($configuration);
            return $detailView->render();
        }
    }

    /**
     * Resolve detail view class name.
     * @return string
     */
    protected function resolveDetailViewClassName($model)
    {
        $modelClassName = get_class($model);
        $parts          = explode('models', $modelClassName);
        return $parts[0] . 'views' . $parts[1] . 'DetailView';
    }
    
    /**
     * Get permission prefix
     * @param ActiveRecord|Array $model
     * @return string
     */
    protected function getPermissionPrefix($model)
    {
        if($model instanceof ActiveRecord)
        {
            return strtolower(UsniAdaptor::getObjectClassName($model));
        }
        return null;
    }
}
?>
