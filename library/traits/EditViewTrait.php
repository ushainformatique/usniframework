<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\traits;

use usni\library\utils\ArrayUtil;
use usni\UsniAdaptor;
use usni\library\exceptions\FailedAfterModelSaveException;
use yii\helpers\Url;
use yii\base\Model;
use usni\library\utils\PermissionUtil;
/**
 * EditViewTrait class file.
 * @package usni\library\traits
 */
trait EditViewTrait
{
    /**
     * Get default configuration for rendering edit view.
     * @param Model $model
     * @return array
     */
    public function getDefaultEditConfiguration($model)
    {
        $editViewClassName  = $this->resolveEditViewClassName();
        $breadCrumbs        = $this->getEditViewBreadcrumb($model);
        return ['className'   => $editViewClassName,
                'breadcrumbs' => $breadCrumbs,
                'controller'  => $this,
                'model'       => $model,
                'redirectUrl' => $this->resolveDefaultRedirectUrl()];
    }

    /**
     * Get breadcrumb for edit view.
     * @param Model $model
     * @return array
     */
    public function getEditViewBreadcrumb($model)
    {
        $modelPluralLabel = $this->resolveModelPluralLabel($model);
        if($model->scenario == 'create')
        {
            return [
                        [
                            'label' => UsniAdaptor::t('application', 'Manage') . ' ' . $modelPluralLabel,
                            'url'   => $this->getBreadCrumbManageUrl()
                        ],
                        [
                            'label' => UsniAdaptor::t('application', 'Create')
                        ]
                    ];
        }
        else
        {
            return [
                        [
                            'label' => UsniAdaptor::t('application', 'Manage') . ' ' . $modelPluralLabel,
                            'url'   => $this->getBreadCrumbManageUrl()
                        ],
                        [
                            'label' => UsniAdaptor::t('application', 'Update') . ' #' . UsniAdaptor::getRequestParam('id')
                        ]
                    ];
        }
    }

    /**
     * Renders edit view.
     * @param array $config Configuration to create and render edit view.
     * The configuration consist of
     * - className
     * - breadcrumbs
     * - scenario
     * - id
     * @return string
     */
    public function renderEditView($config = [])
    {
        $model                      = $this->resolveModel($config);
        $defaultEditConfiguration   = $this->getDefaultEditConfiguration($model);
        $configuration              = ArrayUtil::merge($defaultEditConfiguration, $config);

        $this->getView()->params['breadcrumbs'] = ArrayUtil::popValue('breadcrumbs', $configuration);
        $editViewClass              = ArrayUtil::popValue('className', $configuration);
        $this->processPostData($configuration);
        $editView                   = new $editViewClass($configuration);
        return $editView->render();
    }

    /**
     * Resolve model with edit.
     * @param array $config
     * @throws \yii\web\ForbiddenHttpException()
     */
    protected function resolveModel(& $config = [])
    {
        $model          = ArrayUtil::getValue($config, 'model');
        $scenario       = ArrayUtil::popValue('scenario', $config, 'create');
        $id             = ArrayUtil::popValue('id', $config);
        $modelClassName = ArrayUtil::popValue('modelClassName', $config, $this->resolveModelClassName());
        if( $model != null && $model instanceof Model)
        {
            return $model;
        }
        $user           = UsniAdaptor::app()->user->getUserModel();
        if($scenario == 'create')
        {
            $model = new $modelClassName(['scenario' => 'create']);
        }
        if($scenario == 'update')
        {
            $model              = $this->loadModel($modelClassName, $id);
            $model->scenario    = 'update';
            $module                     = UsniAdaptor::app()->getModule($this->module->id);
            if(method_exists($module, 'getPermissionUtil'))
            {
                $permissionUtil = $module->getPermissionUtil();
            }
            else
            {
                $permissionUtil = PermissionUtil::className();
            }
            $modelClassNameForPerm  = strtolower(UsniAdaptor::getObjectClassName($model));
            $isPermissible          = $permissionUtil::doesUserHavePermissionToPerformAction($model, $user, $modelClassNameForPerm . '.updateother');
            if(!$isPermissible)
            {
                throw new \yii\web\ForbiddenHttpException(\Yii::t('yii','You are not authorized to perform this action.'));
            }
        }
        return $model;
    }

    /**
     * Process post data.
     * @param string $model  Model.
     * @param string $route  Redirect url.
     * @param string $params Params.
     * @return void
     * @catch yii\db\Exception Exception.
     */
    protected function processPostData($config)
    {
        $model      = $config['model'];
        $modelClass = UsniAdaptor::getObjectClassName($model);
        if (isset($_POST[$modelClass]))
        {
            $this->beforeAssigningPostData($model);
            $model->attributes = $_POST[$modelClass];
            if($this->beforeModelSave($model))
            {
                $transaction = UsniAdaptor::db()->beginTransaction();
                try
                {
                    if ($model->save())
                    {
                        if ($this->afterModelSave($model))
                        {
                            $transaction->commit();
                            $redirectUrl = ArrayUtil::getValue($config, 'redirectUrl', 'manage');
                            return $this->redirect(Url::to([$redirectUrl]))->send();
                        }
                        else
                        {
                            $transaction->rollback();
                            throw new FailedAfterModelSaveException(get_class($model));
                        }
                    }
                    else
                    {
                        $transaction->rollback(); 
                    }
                }
                catch (yii\db\Exception $e)
                {
                    $transaction->rollback();
                    throw $e;
                }
            }
        }
    }

    /**
     * Perform changes to the model before saving it.
     * @param string $model ActiveRecord.
     * @return void
     */
    protected function beforeModelSave($model)
    {
        return true;
    }

    /**
     * Perform changes after saving the model.
     * @param ActiveRecord $model
     * @return boolean
     */
    protected function afterModelSave($model)
    {
        return true;
    }

    /**
     * Perform changes to the model before assigning the data from post array.
     * @param ActiveRecord $model
     * @return void
     */
    protected function beforeAssigningPostData($model)
    {

    }

    /**
     * Resolve edit view class name.
     * @return string
     */
    protected function resolveEditViewClassName()
    {
        $modelClassName = $this->resolveModelClassName();
        $parts          = explode('models', $modelClassName);
        return $parts[0] . 'views' . $parts[1] . 'EditView';
    }
}