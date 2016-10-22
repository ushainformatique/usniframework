<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\home\views;

use usni\library\views\UiView;
use usni\UsniAdaptor;
use usni\library\components\UiHtml;

/**
 * DashboardView class file
 * @package usni\library\modules\home\views
 */
class DashboardView extends UiView
{
    /**
     * Render dashboard content.
     * @return string
     */
    protected function renderContent()
    {
        $content    = null;
        $modules    = UsniAdaptor::app()->moduleManager->getInstantiatedModules();
        foreach($modules as $id => $module)
        {
            if(method_exists($module, 'getDashboardContent'))
            {
                $content .= $module->getDashboardContent();
            }
        }
        $content    = UiHtml::tag('div', $content, ['class' => 'row']);
        return $content;
    }
}
?>