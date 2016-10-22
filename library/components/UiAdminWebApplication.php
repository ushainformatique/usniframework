<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\library\components\UiWebApplication;
use usni\UsniAdaptor;
use usni\library\utils\ApplicationUtil;
/**
 * UiAdminWebApplication extends UiWebApplication by providing functions specific to application admin.
 * 
 * @package usni\library\components
 */
class UiAdminWebApplication extends UiWebApplication
{
    /**
     * Module path for admin application.
     * @var string 
     */
    public $modulePath = '@backend/modules';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setHomeUrl(UsniAdaptor::createUrl('home/default/dashboard'));
        ApplicationUtil::loadAdditionalModuleConfig('@common/config/moduleconfig.php');
        ApplicationUtil::loadAdditionalModuleConfig('@backend/config/moduleconfig.php');
    }
}