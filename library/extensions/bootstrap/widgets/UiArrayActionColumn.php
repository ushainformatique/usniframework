<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\widgets;

use usni\library\components\UiHtml;
use usni\fontawesome\FA;
use usni\UsniAdaptor;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\utils\ArrayUtil;

/**
 * UiArrayActionColumn class file.
 * @package usni\library\extensions\bootstrap\widgets
 */
class UiArrayActionColumn extends UiActionColumn
{
    /**
     * @inheritdoc
     */
    protected function renderViewActionLink($url, $model, $key)
    {
        if($this->checkAccess($model, 'view'))
        {
            $shortName  = strtolower(UsniAdaptor::getObjectClassName($this->grid->owner->model));
            $icon       = FA::icon('eye');
            $options    = [
                            'title' => \Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                            'id'        => 'view-' . $shortName . '-' . $model['id'],
                            'class'     => 'view-' . $shortName
                          ];
            if($this->grid->modalDetailView)
            {
                $options = ArrayUtil::merge($options, ['data-toggle'  => 'modal',
                                                       'data-target'  => '#gridContentModal',
                                                       'data-url'     => $url]);
                $url = '#';
            }
            return UiHtml::a($icon, $url, $options);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function renderUpdateActionLink($url, $model, $key)
    {
        if($this->checkAccess($model, 'update'))
        {
            $shortName = strtolower(UsniAdaptor::getObjectClassName($this->grid->owner->model));
            $icon = FA::icon('pencil');
            return UiHtml::a($icon, $url, [
                        'title' => \Yii::t('yii', 'Update'),
                        'data-pjax' => '0',
                        'id'        => 'update-' . $shortName . '-' . $model['id'],
                        'class'     => 'update-' . $shortName
                    ]);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function renderDeleteActionLink($url, $model, $key)
    {
        if($this->checkAccess($model, 'delete'))
        {
            $shortName = strtolower(UsniAdaptor::getObjectClassName($this->grid->owner->model));
            $icon = FA::icon('trash-o');
            return UiHtml::a($icon, $url, [
                        'title' => \Yii::t('yii', 'Delete'),
                        'id'    => 'delete-' . $shortName . '-' . $model['id'],
                        'data-confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function checkAccess($model, $buttonName)
    {
        $user           = UsniAdaptor::app()->user->getUserModel();
        $modelClassName = UsniAdaptor::getObjectClassName($this->grid->owner->model);
        if($user->id != $model['created_by'])
        {
            $buttonName = $buttonName . 'other';
        }
        $permission = strtolower($modelClassName) . '.' . $buttonName;
        if(AuthManager::checkAccess($user, $permission))
        {
            return true;
        }
        return false;
    }
}