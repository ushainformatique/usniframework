<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\components;
/**
 * IAuthIdentity interface is implemented by any active record behaving as a auth identity.
 * An application component represents the authorization identity information
 * for the active record.
 *
 * @package usni.library.modules.auth.components
 */
interface IAuthIdentity extends \yii\web\IdentityInterface
{
    /**
     * Gets auth name for the identity.
     * This would be example username.
     * @return string
     */
    public function getAuthName();

    /**
     * Gets auth type for the identity.
     * This would be example group.
     * @return string
     */
    public function getAuthType();
}
