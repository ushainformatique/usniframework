<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\library\modules\users\utils\UserUtil;
/**
 * UiMaintenanceManager class file.
 * 
 * @package usni\library\components
 */
class UiMaintenanceManager extends \yii\base\Component
{
    /**
     * Maintenance url
     * @var string
     */
    public $url;

    /**
     * Get allowed ips.
     * @return array
     */
    public function getAllowedIps()
    {
        return array(
            //'127.0.0.1'
        );
    }

    /**
     * Checks if user is allowed to access site in maintenance mode.
     * @return boolean
     */
    public function checkAccess()
    {
        $userIp = UserUtil::getUserIpAddress();
        if(in_array($userIp, $this->getAllowedIps()) !== false)
        {
            return true;
        }
        return false;
    }
}