<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\UsniAdaptor;
use usni\library\components\AdminMenuRenderer;
use yii\widgets\Menu;
/**
 * Menu view for admin.
 * @package usni\library\views
 */
class UiMenuView extends UiView
{
    /**
     * Renders content.
     * @return string
     */
    protected function renderContent()
    {
        if(UsniAdaptor::app()->user->isGuest)
        {
            return null;
        }
        $menuItems      = AdminMenuRenderer::getSideBarMenuItems();
        $allMenuItems   = $this->customizeMenuRendering($menuItems);
        return Menu::widget([
            'items'         => $allMenuItems,
            'options'       => ['class' => 'navigation'],
            'encodeLabels' => false,
            'activateParents' => true
        ]);
    }

    /**
     * Customize menu rendering
     * @param array $menuItems
     * @return array
     */
    protected function customizeMenuRendering($menuItems)
    {
        return $menuItems;
    }
}
?>