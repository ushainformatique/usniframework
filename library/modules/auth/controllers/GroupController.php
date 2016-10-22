<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */

namespace usni\library\modules\auth\controllers;

use usni\library\components\UiAdminController;
use usni\library\modules\auth\models\Group;
use yii\helpers\Url;
use usni\library\modules\users\models\User;
use usni\UsniAdaptor;
/**
 * GroupController class file.
 * @package usni\library\modules\auth\controllers
 */
class GroupController extends UiAdminController
{
    use \usni\library\traits\EditViewTranslationTrait;
    
    /**
     * @inheritdoc
     */
    protected function resolveModelClassName()
    {
        return Group::className();
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
     * Create the model.
     * @return string
     */
    public function actionCreate()
    {
        return $this->processEdit('create', null, ['memberModelClasses' => $this->getMemberModelClasses()]);
    }

    /**
     * Update the model.
     * @param int $id
     * @return string
     */
    public function actionUpdate($id)
    {
        return $this->processEdit('update', $id, ['memberModelClasses' => $this->getMemberModelClasses()]);
    }
    
    /**
     * Get member model classes
     * @return array
     */
    protected function getMemberModelClasses()
    {
        return [User::className()];
    }
    
    /**
     * Get page titles.
     * @return array
     */
    public function pageTitles()
    {
        return [
                    'create'       => UsniAdaptor::t('application', 'Create') . ' ' . Group::getLabel(1),
                    'update'       => UsniAdaptor::t('application', 'Update') . ' ' . Group::getLabel(1),
                    'manage'       => UsniAdaptor::t('users', 'Manage') . ' ' . Group::getLabel(1),
                    'view'         => UsniAdaptor::t('users', 'View') . ' ' . Group::getLabel(1),
               ];
    }
}
