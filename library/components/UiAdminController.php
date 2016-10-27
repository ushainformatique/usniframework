<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\UsniAdaptor;
use usni\library\models\GridViewOptionsForm;
use usni\library\modules\users\models\UserMetadata;
use usni\library\components\UiActiveForm;
use usni\library\utils\MetadataUtil;
use yii\helpers\Json;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\exceptions\MethodNotImplementedException;
use usni\library\utils\ArrayUtil;
use usni\library\views\UiErrorView;
use usni\library\views\UiOneColumnView;
use usni\library\utils\ErrorUtil;
use usni\library\filters\UiAccessByPermissionFilter;
use usni\library\utils\AdminUtil;
use usni\library\utils\PermissionUtil;
use yii\web\ForbiddenHttpException;
use usni\library\utils\FileUploadUtil;

/**
 * UiAdminController is the base class for controllers in admin.
 *
 * @package usni\library\components
 */
abstract class UiAdminController extends UiBaseController
{
    use \usni\library\traits\GridViewTrait;
    use \usni\library\traits\DetailViewTrait;
    use \usni\library\traits\EditViewTrait;
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $permissionMap = $this->getActionToPermissionsMap();
        if(empty($permissionMap))
        {
            return [];
        }
        $excludedActions = static::getNonPermissibleActions();
        return [
            'access' => [
                'class' => UiAccessByPermissionFilter::className(),
                'actionToPermissionsMap' => $permissionMap,
                'except' => $excludedActions,
            ],
        ];
    }
    
    /**
     * Index action
     * @return string
     */
    public function actionIndex()
    {
        return $this->actionManage();
    }
    
    /**
     * Create the model.
     * @return string
     */
    public function actionCreate()
    {
        return $this->processEdit('create');
    }

    /**
     * Update the model.
     * @param int $id
     * @return string
     */
    public function actionUpdate($id)
    {
        return $this->processEdit('update', $id);
    }

    /**
     * Manages models.
     * @return string
     */
    public function actionManage($config = [])
    {
        return $this->processManage($config);
    }

    /**
     * Delete model
     * @param int $id
     * @return string
     */
    public function actionDelete($id)
    {
        return $this->processDelete($id);
    }

    /**
     * View model
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->processView($id);
    }

    /**
     * Sets the breadcrumbs for the currently executed action.
     * @param string $model Model Instance.
     * @return void
     */
    public function setBreadCrumbs($model)
    {
        $breadcrumbs    = [];
        $action         = $this->action->id;
        switch ($action)
        {
            case 'create':
            case 'update':
                $breadcrumbs = $this->getEditViewBreadcrumb($model);
                break;
            case 'manage':
                $breadcrumbs = $this->getGridViewBreadcrumb($model);
                break;
            case 'view':
                $breadcrumbs = $this->getDetailViewBreadcrumb($model);
                break;
            default:break;
        }
        $this->getView()->params['breadcrumbs']  = $breadcrumbs;
    }

    /**
     * Get breadcrumb manage url.
     * @return array
     */
    protected function getBreadCrumbManageUrl()
    {
        return ['/' . $this->module->id . '/' . $this->id . '/manage'];
    }

    /**
     * Apply grid view settings.
     * @return void
     */
    public function actionGridViewSettings()
    {
        $userId        = UsniAdaptor::app()->user->getUserModel()->id;
        $formClassName = GridViewOptionsForm::className();
        $model         = new $formClassName();
        if($model->load($_POST) !== false)
        {
            $status = 'failure';
            $model->availableColumns = $model->availableColumns == null ? [] : $model->availableColumns;
            $model->displayedColumns = $model->displayedColumns == null ? [] : $model->displayedColumns;
            $errors  = UiActiveForm::validate($model);
            if(empty($errors))
            {
                $metadataRecord     = MetadataUtil::getUserMetaDataRecordForClassName($model->viewClassName, $userId);
                if($metadataRecord === false)
                {
                    $metadataRecord = new UserMetadata();
                }
                MetadataUtil::saveRecord($metadataRecord, $model->viewClassName, $userId, $model->getAttributes());
                $status = 'success';
            }
            echo Json::encode(['status' => $status, 'errors' => $errors]);
            UsniAdaptor::app()->end();
        }
    }

    /**
     * Process edit.
     * @param string $model
     * @param string $id
     * @return string
     */
    protected function processEdit($scenario,
                                   $id = null,
                                   $config = [])
    {
        $config['scenario'] = $scenario;
        $config['id']       = $id;
        $output             = $this->renderEditView($config);
        if(UsniAdaptor::getRequest()->getIsAjax())
        {
            return $output;
        }
        else
        {
            $content            = $this->renderColumnContent($output);
            return $this->render($this->getDefaultLayout(), array('content' => $content));
        }
    }

    /**
     * Process manage.
     * @param array $config Configuration to create and render grid view.
     * @see GridViewTrait::renderGridView
     * @return string
     */
    protected function processManage($config = [])
    {
        $gridView = $this->renderGridView($config);
        $content  = $this->renderColumnContent($gridView);
        return $this->render($this->getDefaultLayout(), ['content' => $content]);
    }

    /**
     * Process delete.
     * @param int $id
     * @param array $config
     * @return void
     */
    protected function processDelete($id, $config = [])
    {
        $user           = UsniAdaptor::app()->user->getUserModel();
        $modelClassName = ArrayUtil::getValue($config, 'modelClassName', $this->resolveModelClassName());
        $redirectUrl    = ArrayUtil::getValue($config, 'redirectUrl', $this->resolveDefaultRedirectUrl());
        $model          = $this->loadModel($modelClassName, $id);
        $modelClassNameForPerm = strtolower(UsniAdaptor::getObjectClassName($model));
        $isPermissible  = PermissionUtil::doesUserHavePermissionToPerformAction($model, $user, $modelClassNameForPerm . '.deleteother');
        if(!$isPermissible)
        {
            throw new ForbiddenHttpException(\Yii::t('yii','You are not authorized to perform this action.'));
        }
        else
        {
            $this->deleteModel($model);
            return $this->redirect([$redirectUrl]);
        }
    }
    
    /**
     * Deletes model
     * @param Model $model
     * @return boolean
     */
    protected function deleteModel($model)
    {
        try
        {
            return $model->delete();
        }
        catch (\Exception $ex)
        {
            $message = UsniAdaptor::t('applicationflash', 'Delete failed due to {error}', ['error' => $ex->getMessage()]);
            UsniAdaptor::app()->getSession()->setFlash('deleteFailed', $message);
            return false;
        }
    }

    /**
     * Process view.
     * @param int $id
     * @param array $config
     * @return void
     */
    protected function processView($id, $config = [])
    {
        if(UsniAdaptor::getRequest()->getIsAjax())
        {
            $content = $this->renderDetailView($id, $config);
            return $this->renderAjax('@usni/themes/bootstrap/views/layouts/ajaxview', ['content' => $content]);
        }
        else
        {
            $content  = $this->renderColumnContent($this->renderDetailView($id, $config));
            return $this->render($this->getDefaultLayout(), ['content' => $content]);
        }
    }

    /**
     * Resolve model class name for the controller.
     * @return null
     */
    protected function resolveModelClassName()
    {
        throw new MethodNotImplementedException(__FUNCTION__, get_class($this));
    }

    /**
     * Resolve default redirect url for the controller.
     * @return null
     */
    protected function resolveDefaultRedirectUrl()
    {
        return '/' . $this->module->id . '/' . $this->id . '/manage';
    }

    /**
	 * This is the action to handle external exceptions.
     * @return void
	 */
	public function actionError()
	{
        $errorHandler   = UsniAdaptor::app()->errorHandler;
        $error          = $errorHandler->exception;
        if($error != null)
		{
            $errorInfo          = ErrorUtil::getInfo($error, $errorHandler);
            $view               = new UiOneColumnView();
            $view->addContainedView(new UiErrorView($error, $errorInfo));
            $content            = $view->render();
            return $this->render($this->getDefaultLayout(), array('content' => $content, 'title' => $errorInfo['pageTitle']));
		}
	}

    /**
     * Resolves model plural label.
     * @param string $model
     * @return string
     */
    protected function resolveModelPluralLabel($model)
    {
        return $model->getLabel(2);
    }

    /**
     * Get default layout.
     * @return string
     */
    protected function getDefaultLayout()
    {
        return '@usni/themes/bootstrap/views/layouts/main';
    }

    /**
     * Get action to permission map.
     * @return array
     */
    protected function getActionToPermissionsMap()
    {
        $modelClassName                  = '';
        $fullyQualifiedModelClassName    = $this->resolveModelClassName();
        if(!empty($fullyQualifiedModelClassName))
        {
            $model          = new $fullyQualifiedModelClassName();
            $modelClassName = UsniAdaptor::getObjectClassName($model);
        }
        if($modelClassName != null)
        {
            $modelClassName = strtolower($modelClassName);
            return array('create'     => $modelClassName . '.create',
                         'update'     => $modelClassName . '.update',
                         'view'       => $modelClassName . '.view',
                         'manage'     => $modelClassName . '.manage',
                         'delete'     => $modelClassName . '.delete',
                         'bulk-edit'   => $modelClassName . '.bulk-edit',
                         'bulk-delete' => $modelClassName . '.bulk-delete',
                         'grid-view-settings' => $modelClassName . '.manage'
                        );
        }
        return array();
    }

    /**
     * Gets non permissible actions.
     * @return array
     */
    protected static function getNonPermissibleActions()
    {
        return ['login', 'logout', 'error', 'delete-image'];
    }

    /**
     * Perform BulkDelete on gridview
     * @return void
     */
    public function actionBulkDelete()
    {
        $user     = UsniAdaptor::app()->user->getUserModel();
        if (UsniAdaptor::app()->request->isAjax && isset($_GET['id']))
        {
            $modelClass             = ucfirst($this->resolveModelClassName());
            $model                  = new $modelClass();
            $modelPermissionName    = UsniAdaptor::getObjectClassName($model);
            $selectedItems          = $_GET['id'];
            $redirectUrl            = $this->resolveDefaultRedirectUrl();
            foreach ($selectedItems as $item)
            {
                if(!in_array($item, $this->getExcludedModelIdsFromBulkDelete()))
                {
                    $model = $modelClass::findOne(intval($item));
                    //Check if allowed to delete
                    if(($model['created_by'] == $user->id && AuthManager::checkAccess($user, strtolower($modelPermissionName) . '.delete')) ||
                            ($model['created_by'] != $user->id && AuthManager::checkAccess($user, strtolower($modelPermissionName) . '.deleteother')))
                    {
                        $model->delete();
                    }
                }
            }
        }
    }

    /**
     * Perform BulkEdit on gridview
     *
     * @param string $modelClassName
     * @param string $selectedIds
     *
     * @return void
     */
    public function actionBulkEdit($modelClassName, $selectedIds)
    {
        $modelClass         = UsniAdaptor::getObjectClassName($modelClassName);
        $selectedIdData     = explode(',', $selectedIds);
        $model              = new $modelClassName();
        $model->scenario    = 'bulkedit';
        if(UsniAdaptor::app()->request->isAjax && isset($_POST[$modelClass]))
        {
            $this->processBulkEditUpdate($modelClassName, $model, $selectedIdData);
        }
        $editView           = $this->resolveBulkEditViewClassName();
        if($editView == null)
        {
            $parts      = explode('models', $modelClassName);
            $editView   = $parts[0] . 'views' . $parts[1] . 'BulkEditView';
        }
        $bulkEditView       = new $editView($model, $selectedIds, $pjaxId = '');
        $output             = $bulkEditView->render();
        echo $this->renderContent($output);
    }

    /**
     * Get bulk edit view class name
     * @return null
     */
    protected function resolveBulkEditViewClassName()
    {
        return null;
    }

    /**
     * Process bulk edit update
     * @param string $modelClassName
     * @param Model $model
     * @param string $selectedIdData
     */
    protected function processBulkEditUpdate($modelClassName, $model, $selectedIdData)
    {
        $excludedSelectedIds = $this->getExcludedModelIdsFromBulkUpdate();
        $modifiedSelectedIds = AdminUtil::getModifiedSelectedIdsForBulkEdit($excludedSelectedIds, $selectedIdData);
        $formData            = $_POST[UsniAdaptor::getObjectClassName($modelClassName)];
        if(!empty($modifiedSelectedIds))
        {
            foreach ($formData as $key => $value)
            {
                foreach ($modifiedSelectedIds as $id)
                {
                    $this->updateModelAttributeWithBulkEdit($modelClassName, $id, $key, $value);
                }
            }
        }
    }
    
    /**
     * Update model attribute value with bulk edit
     * @param string $modelClassName
     * @param int $id
     * @param string $key
     * @param mixed $value
     */
    protected function updateModelAttributeWithBulkEdit($modelClassName, $id, $key, $value)
    {
        $model              = $modelClassName::findOne($id);
        $model->scenario    = 'bulkedit';
        $model->$key        = $value;
        $model->save();
    }

    /**
     * Get excluded models from bulk update.
     * @return array
     */
    protected function getExcludedModelIdsFromBulkUpdate()
    {
        return array();
    }

    /**
     * Get excluded models from bulk update.
     * @return array
     */
    protected function getExcludedModelIdsFromBulkDelete()
    {
        return array();
    }
    
    /**
     * Delete image.
     * @param integer $id.
     * @return void.
     */
    public function actionDeleteImage()
    {
        if(UsniAdaptor::app()->request->isAjax)
        {
            $id         = $_GET['id'];
            $modelClass = null;
            if($_GET['modelClass'] != null)
            {
                $modelClass = base64_decode($_GET['modelClass']);
            }
            else
            {
                $modelClass = $this->resolveModelClassName();
            }
            $model              = $modelClass::findOne($id);
            $imageFieldName     = $this->getImageFieldName();
            $model->scenario    = 'deleteimage';
            FileUploadUtil::deleteImage($model, $imageFieldName, 150, 150);
            $model->$imageFieldName = null;
            $model->save();
            
        }
    }
    
    /**
     * Get image field name.
     * @return string
     */
    protected function getImageFieldName()
    {
        return 'image';
    }
}