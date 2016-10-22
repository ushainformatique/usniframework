<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\service\views;

use usni\library\views\UiView;
use usni\UsniAdaptor;
/**
 * RebuildApplicationView class file.
 * 
 * @package usni\library\modules\service\views
 */
class RebuildApplicationView extends UiView
{
    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $file = UsniAdaptor::getAlias('@usni/themes/bootstrap/views/services/rebuildapp.php');
        return $this->getView()->renderPhpFile($file);
    }
}