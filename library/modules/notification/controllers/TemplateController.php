<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\controllers;

use usni\library\components\UiAdminController;
use usni\library\modules\notification\models\NotificationTemplate;
use usni\UsniAdaptor;
use usni\library\modules\notification\models\NotificationLayout;
use usni\library\utils\ConfigurationUtil;
use yii\helpers\Url;
/**
 * TemplateController class file
 * @package usni\library\modules\notification\controllers
 */
class TemplateController extends UiAdminController
{
    use \usni\library\traits\EditViewTranslationTrait;
    
    /**
     * @inheritdoc
     */
    protected function resolveModelClassName()
    {
        return NotificationTemplate::className();
    }
    
    /**
     * Action Preview to render preview of Notification Template.
     * @return void
     */
    public function actionPreview()
    {
        $modelName       = $this->resolveModelClassName();
        $modelClass      = UsniAdaptor::getObjectClassName($modelName);
        if(UsniAdaptor::app()->request->isAjax && $_REQUEST[$modelClass]['layout_id'] != null)
        {
            $layout = NotificationLayout::findOne($_REQUEST[$modelClass]['layout_id']);
        }
        else
        {
            $layout['content'] = '{{####content####}}';
        }
        return str_replace(
                            [
                                '{{####title####}}', 
                                '{{####description####}}',
                                '{{####content####}}'
                            ],
                            [
                                ConfigurationUtil::getValue('application', 'siteName'), 
                                ConfigurationUtil::getValue('application', 'siteDescription'), 
                                $_REQUEST[$modelClass]['content']
                            ],
                            $layout['content']);
    }
    
    /**
     * Action Preview to render preview of Notification Template.
     * @return void
     */
    public function actionGridPreview($id)
    {
        $modelName              = $this->resolveModelClassName();
        $modelClass             = UsniAdaptor::getObjectClassName($modelName);
        $notificationTemplate   = $modelName::findOne($id);
        if(UsniAdaptor::app()->request->isAjax && $notificationTemplate->layout_id != null)
        {
            $layout = NotificationLayout::findOne($notificationTemplate->layout_id);
        }
        else
        {
            $layout['content'] = '{{####content####}}';
        }
        return str_replace(
                            [
                                '{{####title####}}', 
                                '{{####description####}}', 
                                '{{####content####}}'
                            ],
                            [
                                ConfigurationUtil::getValue('application', 'siteName'), 
                                ConfigurationUtil::getValue('application', 'siteDescription'), 
                                $notificationTemplate->content
                            ],
                            $layout['content']);
    }

    /**
     * Get action to permission map.
     * @return array
     */
    protected function getActionToPermissionsMap()
    {
        $modelClassName             = $this->resolveModelClassName();
        $permissionsMap             = parent::getActionToPermissionsMap();
        $permissionsMap['preview']  = strtolower($modelClassName) . '.preview';
        return $permissionsMap;
    }
    
    /**
     * Force delete a model
     * @param string $notifyKey
     * @return void
     */
    public function actionForceDelete($notifyKey)
    {
        $modelClassName             = $this->resolveModelClassName();
        $translatedModelClassName   = $this->resolveModelClassName() . 'Translated';
        $data = $modelClassName::find()->where('notifykey = :notifykey', [':notifykey' => $notifyKey])->one();
        if(!empty($data))
        {
           $modelClassName::deleteAll('id = :id', [':id' => $data->id]);
           $translatedModelClassName::deleteAll('owner_id = :owner_id', [':owner_id' => $data->id]);
           $this->redirect(Url::to($this->getBreadCrumbManageUrl(), true));
        }
        $this->redirect(Url::to($this->getBreadCrumbManageUrl(), true));
    }
    
    /**
     * @inheritdoc
     */
    public function pageTitles()
    {
        return [
                    'create'         => UsniAdaptor::t('application','Create') . ' ' . NotificationTemplate::getLabel(1),
                    'update'         => UsniAdaptor::t('application','Update') . ' ' . NotificationTemplate::getLabel(1),
                    'view'           => UsniAdaptor::t('application','View') . ' ' . NotificationTemplate::getLabel(1),
                    'manage'         => UsniAdaptor::t('application','Manage') . ' ' . NotificationTemplate::getLabel(1)
               ];
    }
}