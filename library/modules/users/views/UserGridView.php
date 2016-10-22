<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\components\UiGridView;
use usni\UsniAdaptor;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\modules\users\models\User;
use usni\library\modules\users\components\UserActionColumn;
use usni\library\components\UiHtml;
use usni\library\utils\TimezoneUtil;
use usni\library\modules\users\widgets\UserNameDataColumn;
use usni\library\extensions\bootstrap\widgets\UserGridViewActionToolBar;
use usni\library\utils\FlashUtil;
/**
 * User Grid View.
 * @package usni\library\modules\users\views
 */
class UserGridView extends UiGridView
{
    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        $filterParams = UsniAdaptor::app()->request->get($this->getFilterModelClass());
        $columns = [
            [
                'attribute' => 'username',
                'class'     => UserNameDataColumn::className()
            ],
            [
                'label'     => UsniAdaptor::t('users', 'Email'),
                'attribute' => 'person.email',
                'filter'    => UiHtml::textInput(UiHtml::getInputName($this->filterModel, 'email'), $filterParams['email'], $this->getDefaultFilterOptions())
            ],
            [
                'label'     => UsniAdaptor::t('users', 'First Name'),
                'attribute' => 'person.firstname',
                'filter'    => UiHtml::textInput(UiHtml::getInputName($this->filterModel, 'firstname'), $filterParams['firstname'], $this->getDefaultFilterOptions())
            ],
            [
                'label' => UsniAdaptor::t('users', 'Last Name'),
                'attribute' => 'person.lastname',
                'filter'    => UiHtml::textInput(UiHtml::getInputName($this->filterModel, 'lastname'), $filterParams['lastname'], $this->getDefaultFilterOptions())
            ],
            [
                'attribute' => 'timezone',
                'filter'    => TimezoneUtil::getTimezoneSelectOptions()
            ],
            [
                'attribute' => 'address.address1',
                'filter'    => UiHtml::textInput(UiHtml::getInputName($this->filterModel, 'address1'), $filterParams['address1'], $this->getDefaultFilterOptions())
            ],
            [
                'attribute' => 'status',
                'class'     => 'usni\library\modules\users\widgets\UserStatusDataColumn',
                'filter'    => User::getStatusDropdown()
            ],
            [
                'class'     => $this->resolveActionColumnClassName(),
                'template'  => '{view} {update} {changepassword} {changestatus}'
            ],
        ];
        return $columns;
    }

    /**
     * @inheritdoc
     */
    protected function getTitle()
    {
        return UsniAdaptor::t('users', 'Manage Users');
    }

    /**
     * @inheritdoc
     */
    protected function resolveDataProviderSort()
    {
        return [
                'defaultOrder' => ['username' => SORT_ASC],
                'attributes'   => ['username', 'person.email', 'person.firstname', 'person.lastname', 'timezone', 'status',
                                   'address.address1']
               ];
    }

    /**
     * Should checkbox for the row be disabled.
     * @param int $row
     * @param Model $data
     * @return boolean
     */
    protected function shouldCheckBoxBeDisabled($data, $row)
    {
        $user           = UsniAdaptor::app()->user->getUserModel();
        if($user->id == $data->id || $user->id == $data->created_by)
        {
            if(AuthManager::checkAccess($user, 'user.update') || AuthManager::checkAccess($user, 'user.delete'))
            {
                return false;
            }
        }
        else
        {
            if(AuthManager::checkAccess($user, 'user.updateother') || AuthManager::checkAccess($user, 'user.deleteother'))
            {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public static function getGridViewActionToolBarClassName()
    {
        return UserGridViewActionToolBar::className();
    }
    
    /**
     * Resolve action column class name.
     * @return string
     */
    protected function resolveActionColumnClassName()
    {
        return UserActionColumn::className();
    }
    
    /**
     * @inheritdoc
     */
    protected static function getActionToolbarOptions()
    {
        $options = parent::getActionToolbarOptions();
        $options['showBulkDelete'] = false;
        return $options;
    }
    
    /**
     * @inheritdoc
     */
    protected function renderFlashMessages()
    {
        return FlashUtil::render('userregistration', 'alert alert-success');
    }
}
?>