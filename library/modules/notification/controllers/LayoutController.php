<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\controllers;

use usni\library\components\UiAdminController;
use usni\library\modules\notification\models\NotificationLayout;
use yii\helpers\Url;
use usni\UsniAdaptor;
/**
 * LayoutController class file
 * @package usni\library\modules\notification\controllers
 */
class LayoutController extends UiAdminController
{
    use \usni\library\traits\EditViewTranslationTrait;
    
    /**
     * @inheritdoc
     */
    protected function resolveModelClassName()
    {
        return NotificationLayout::className();
    }
    
    /**
     * Force delete a model
     * @param string $alias
     * @return void
     */
    public function actionForceDelete($name)
    {
        $modelClassName             = $this->resolveModelClassName();
        $translatedModelClassName   = $this->resolveModelClassName() . 'Translated';
        $data = $translatedModelClassName::find()->where('name = :name', [':name' => $name])->one();
        if(!empty($data))
        {
           $modelClassName::deleteAll('id = :id', [':id' => $data->owner_id]);
           $translatedModelClassName::deleteAll('name = :name', [':name' => $name]);
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
                    'create'         => UsniAdaptor::t('application','Create') . ' ' . NotificationLayout::getLabel(1),
                    'update'         => UsniAdaptor::t('application','Update') . ' ' . NotificationLayout::getLabel(1),
                    'view'           => UsniAdaptor::t('application','View') . ' ' . NotificationLayout::getLabel(1),
                    'manage'         => UsniAdaptor::t('application','Manage') . ' ' . NotificationLayout::getLabel(2)
               ];
    }
}