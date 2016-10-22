<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\modules\users\utils\UserUtil;
use usni\UsniAdaptor;
use usni\library\modules\users\views\UserBrowseModelView;
/**
 * Browse model view for user.
 * 
 * @package usni\library\modules\users\views
 */
class UserEditBrowseModelView extends UserBrowseModelView
{
    /**
     * @inheritdoc
     */
    protected function resolveDropdownData()
    {
        return UserUtil::getBrowseByDropDownOptions($this->model, $this->attribute, 'user.updateother', UsniAdaptor::app()->user->getUserModel());
    }

}