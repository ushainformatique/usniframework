<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\install\views;

use usni\library\views\UiView;
use usni\library\utils\ArrayUtil;
use usni\UsniAdaptor;
/**
 * InstallCheckSystemView class file.
 * @package usni\library\modules\install\views
 */
class InstallCheckSystemView extends UiView
{
    /**
     * Results for the system check.
     * @var array
     */
    protected $requirements;
    /**
     * Results for the system check.
     * @var array
     */
    protected $summary;

    /**
     * Class constructor.
     * @param array $systemResults
     */
    public function __construct($systemResults)
    {
        $this->requirements  = ArrayUtil::getValue($systemResults, 'requirements');
        $this->summary       = ArrayUtil::getValue($systemResults, 'summary');
    }

    /**
     * Renders content.
     * @return string
     */
    protected function renderContent()
    {
        $file = UsniAdaptor::getAlias($this->getViewFile());
        return $this->getView()->renderPhpFile($file,
                                                    array('requirements' => $this->requirements,
                                                          'summary'      => $this->summary));
    }
    
    /**
     * Gets view file.
     * @return string
     */
    protected function getViewFile()
    {
        return '@usni/themes/bootstrap/views/install/settings.php';
    }
}
?>