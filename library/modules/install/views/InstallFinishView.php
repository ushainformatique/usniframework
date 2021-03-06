<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\install\views;

use usni\library\views\UiView;
use usni\UsniAdaptor;
/**
 * InstallFinishView class file.
 * 
 * @package usni\library\modules\install\views
 */
class InstallFinishView extends UiView
{
    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $file = UsniAdaptor::getAlias('@usni/themes/bootstrap/views/install/final.php');
        return $this->getView()->renderPhpFile($file);
    }
}
?>