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
 * UiActionColumn class file.
 *
 * @package usni\library\extensions\bootstrap\widgets
 */
/**
 * Bootstrap action column widget.
 */
class UiActionColumn extends \yii\grid\ActionColumn
{
    /**
     * Initializes the default button rendering callbacks
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['view']))
        {
            $this->buttons['view'] = array($this, 'renderViewActionLink');
        }
        if (!isset($this->buttons['update']))
        {
            $this->buttons['update'] = array($this, 'renderUpdateActionLink');
        }
        if (!isset($this->buttons['delete']))
        {
            $this->buttons['delete'] = array($this, 'renderDeleteActionLink');;
        }
    }

    /**
     * Renders view action link.
     * @param string $url
     * @param Model $model
     * @param string $key
     * @return string
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
     * Renders update action link.
     * @param string $url
     * @param Model $model
     * @param string $key
     * @return string
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
     * Renders delete action link.
     * @param string $url
     * @param Model $model
     * @param string $key
     * @return string
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
     * Resolve action button visiblity
     * @param Model $model
     * @param string $buttonName
     * @return boolean
     */
    protected function checkAccess($model, $buttonName)
    {
        $user           = UsniAdaptor::app()->user->getUserModel();
        $modelClassName = $this->getModelClassName();
        if($user['id'] != $model['created_by'])
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
    
    /**
     * Get model class name
     * @return type
     */
    protected function getModelClassName()
    {
        return UsniAdaptor::getObjectClassName($this->grid->owner->model);
    }
}