<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\utils;

use usni\library\extensions\bootstrap\widgets\UiActionColumn;
use usni\UsniAdaptor;
use usni\fontawesome\FA;
use usni\library\components\UiHtml;
/**
 * ModuleSettingsActionColumn class file.
 *
 * @package usni\library\modules\users\components
 */
class ModuleSettingsActionColumn extends UiActionColumn
{
    /**
     * Initializes the default button rendering callbacks
     */
    protected function initDefaultButtons()
    {
        parent::initDefaultButtons();
        if (!isset($this->buttons['changestatus']))
        {
            $this->buttons['changestatus'] = [$this, 'renderChangeStatusLink'];
        }
    }

    /**
     * Renders change status link.
     * @param string $url
     * @param Model $model
     * @param string $key
     * @return string
     */
    public function renderChangeStatusLink($url, $model, $key)
    {
        if($model['canBeDisabled'] == false)
        {
            return null;
        }
        if($model['status'] == 1)
        {
            $label = UsniAdaptor::t('application', 'Deactivate Module');
            $icon  = FA::icon('close');
            $url   = UsniAdaptor::createUrl('settings/default/change-status', ['id' => $model['id'], 'status' => 0]);
        }
        elseif($model['status'] == 0)
        {
            $label = UsniAdaptor::t('application', 'Activate Module');
            $icon  = FA::icon('check');
            $url   = UsniAdaptor::createUrl('settings/default/change-status', ['id' => $model['id'], 'status' => 1]);
        }
        return UiHtml::a($icon, $url, ['title' => $label, 'data-pjax' => '1', 'class' => 'change-status']);
    }
    
    /**
     * @inheritdoc
     */
    protected function checkAccess($model, $buttonName)
    {
        return false;
    }
}