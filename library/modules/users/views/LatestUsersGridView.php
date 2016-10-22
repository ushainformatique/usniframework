<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\UsniAdaptor;
use usni\library\components\UiGridView;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\modules\users\models\User;
use yii\data\ActiveDataProvider;
use usni\library\modules\users\widgets\UserNameDataColumn;
use usni\library\modules\users\widgets\UserStatusDataColumn;
/**
 * Latest Users Grid View.
 * @package usni\library\modules\users\views
 */
class LatestUsersGridView extends UiGridView
{
    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        $columns = [
            [
                'attribute' => 'username',
                'class'     => UserNameDataColumn::className(),
                'enableSorting' => false
            ],
            [
                'label'     => UsniAdaptor::t('users', 'Email'),
                'value'     => 'person.email'
            ],
            [
                'attribute'  => 'status',
                'class'      => UserStatusDataColumn::className(),
                'enableSorting' => false
            ],
        ];
        return $columns;
    }

    /**
     * @inheritdoc
     */
    protected function getTitle()
    {
        return UsniAdaptor::t('users', 'Latest Users');
    }

    /**
     * @inheritdoc
     */
    protected function getDataProvider()
    {
        $user       = UsniAdaptor::app()->user->getUserModel();
        $query      = User::find()->where('id != :id', [':id' => User::SUPER_USER_ID])->orderBy('id DESC');
        if(!(AuthManager::isUserInAdministrativeGroup($user)
                    && AuthManager::isSuperUser($user)) && !AuthManager::checkAccess($user, 'user.viewother'))
        {
            $query->andFilterWhere(['created_by' => $user->id]);
        }
        $query->limit(5);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        $dataProvider->setPagination(false);
        return $dataProvider;
    }

    /**
     * @inheritdoc
     */
    protected function renderToolbar()
    {
        return null;
    }
    
    /**
     * @inheritdoc
     */
    protected function renderCheckboxColumn()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function getLayout()
    {
        return "<div class='panel panel-default'><div class='panel-heading'>{caption}</div>\n{items}</div>";
    }
}
?>