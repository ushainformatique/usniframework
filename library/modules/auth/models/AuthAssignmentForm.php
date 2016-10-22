<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\models;

use usni\library\components\UiFormModel;
use usni\UsniAdaptor;
use usni\library\modules\auth\managers\AuthManager;
/**
 * AuthAssignmentForm class file.
 * @package usni\library\modules\auth\models
 */
class AuthAssignmentForm extends UiFormModel
{
    /**
     * Permissions in the system.
     * @var array
     */
    public $permissions;
    /**
     * Auth identity id.
     * @var int
     */
    public $authIdentityId;
    /**
     * Auth identity type.
     * @var string
     */
    public $authType;
    /**
     * Associated auth assignments for the auth identity.
     * @var array
     */
    public $authAssignments;
    /**
     * Auth Identity Object
     * @var Object
     */
    public $authIdentity;

    /**
     * @inheritdoc
     */
    public function __construct($authIdentityId, $authType)
    {
        $this->authIdentityId   = $authIdentityId;
        $this->authType         = $authType;
        $this->permissions      = AuthManager::getAllPermissionsList();
        if($this->authIdentityId != null && $this->authType != null)
        {
            $this->authIdentity     = AuthManager::getAuthIdentity($this->authIdentityId, $this->authType);
            $this->authAssignments  = AuthManager::getAuthAssignments($this->authIdentity->getAuthName(),
                                                                                  $this->authIdentity->getAuthType());
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                    [['authIdentityId', 'authType'],                    'required'],
                    [['authIdentityId', 'authType', 'permissions'],     'safe'],
                    ['authIdentityId',                                  'number'],
                    ['authType',                                        'string'],
               ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
                    'permissions' => UsniAdaptor::t('auth', 'Permissions'),
               ];
    }

    /**
     * Get attribute hints.
     * @return array
     */
    public function attributeHints()
    {
        return array();
    }
}
?>