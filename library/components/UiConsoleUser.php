<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\library\modules\users\models\User;

/**
 * UiConsoleUser class file for console application.
 * @package usni\library\components
 */
class UiConsoleUser extends \yii\web\User
{
    /**
	 * Returns user model.
	 * @return mixed the unique identifier for the user. If null, it means the user is a guest.
	 */
	public function getUserModel()
	{
        return User::findOne(User::SUPER_USER_ID);
	}
}
?>