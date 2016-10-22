<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\views\UiBrowseModelView;
use usni\library\modules\users\models\User;
/**
 * Browse model view for user.
 * @package usni\library\modules\users\views
 */
class UserBrowseModelView extends UiBrowseModelView
{
    /**
     * @inheritdoc
     */
    protected function resolveDropdownData()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function unsetNotAllowed(& $data)
    {
        unset($data[$_GET['id']]);
        unset($data[User::SUPER_USER_ID]);
    }
}
?>