<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\service\views;

use usni\library\views\UiView;
use usni\UsniAdaptor;
/**
 * IndexView class file.
 * @package usni\library\modules\service\views
 */
class IndexView extends UiView
{
    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        return UsniAdaptor::app()->controller->renderPartial('@usni/themes/bootstrap/views/services/_index', 
                                                ['title'   => UsniAdaptor::t('service', 'Services'),
                                                 'footer'  => '']);
    }
}
?>
