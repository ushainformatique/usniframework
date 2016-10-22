<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\components;

use usni\library\extensions\bootstrap\widgets\UiActionColumn;
use usni\UsniAdaptor;
use usni\fontawesome\FA;
use usni\library\components\UiHtml;
use usni\library\modules\auth\managers\AuthManager;
/**
 * AuthActionColumn class file.
 * 
 * @package usni\library\modules\auth\components
 */
class AuthActionColumn extends UiActionColumn
{
    /**
     * @inheritdoc
     */
    protected function initDefaultButtons()
    {
        parent::initDefaultButtons();
        if (!isset($this->buttons['managepermissions']))
        {
            $this->buttons['managepermissions'] = [$this, 'renderPermissionLink'];
        }
    }

    /**
     * Renders manage permission link.
     * @param string $url
     * @param Model $model
     * @param string $key
     * @return string
     */
    public function renderPermissionLink($url, $model, $key)
    {
        if($this->checkAccess($model, 'managepermissions'))
        {
            $shortName  = strtolower(UsniAdaptor::getObjectClassName($model));
            $label = UsniAdaptor::t('auth', 'Manage Permissions');
            $icon  = FA::icon('lock');
            $url   = UsniAdaptor::createUrl("auth/permission/group", array("id" => $model->id));
            return UiHtml::a($icon, $url, [
                                                'title' => $label,
                                                'data-pjax' => '0',
                                                'id'    => 'permission-' . $shortName . '-' . $model->id
                                          ]);
        }
        return null;
    }
    
    /**
     * Resolve action button visiblity
     * @param Model $model
     * @param string $buttonName
     * @return boolean
     */
    protected function checkAccess($model, $buttonName)
    {
        if($buttonName === 'managepermissions')
        {
            $user       = UsniAdaptor::app()->user->getUserModel();
            $permission = 'auth.' . $buttonName;
            if(AuthManager::checkAccess($user, $permission))
            {
                return true;
            }
            return false;
        }
        else
        {
            return parent::checkAccess($model, $buttonName);
        }
    }
}