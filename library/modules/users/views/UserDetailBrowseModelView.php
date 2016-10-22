<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\modules\users\views\UserBrowseModelView;
use usni\library\modules\users\utils\UserUtil;
use usni\UsniAdaptor;
/**
 * UserDetailBrowseModelView class file.
 *
 * @package usni\library\modules\users\views
 */
class UserDetailBrowseModelView extends UserBrowseModelView
{
    /**
     * @inheritdoc
     */
    protected function resolveDropdownData()
    {
        return UserUtil::getBrowseByDropDownOptions($this->model, $this->attribute, 'user.viewother', UsniAdaptor::app()->user->getUserModel());
    }

}