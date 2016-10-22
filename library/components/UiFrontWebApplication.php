<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\library\components\UiWebApplication;
use usni\library\utils\ApplicationUtil;
/**
 * UiFrontWebApplication class file.
 *
 * @package usni\library\components
 */
class UiFrontWebApplication extends UiWebApplication
{
    /**
     * Module path for admin application.
     * @var string 
     */
    public $modulePath = '@frontend/modules';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        ApplicationUtil::loadAdditionalModuleConfig('@common/config/moduleconfig.php');
        ApplicationUtil::loadAdditionalModuleConfig('@frontend/config/moduleconfig.php');
    }
}