<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\filters;

use usni\UsniAdaptor;
use yii\helpers\Url;

/**
 * Filter that automatically checks if the request is for maintenance and redirects appropriately
 * 
 * @package usni\library\filters
 */
class MaintenanceFilter extends \yii\base\ActionFilter
{
    /**
     * @inheritdoc
     */
	public function beforeAction($action)
	{
        $currentUrl   = Url::to('');
        $isInstalled  = UsniAdaptor::app()->installed;
        if($isInstalled)
        {
            if(UsniAdaptor::app()->isMaintenanceMode() === true && strpos($currentUrl, 'maintenance') === false)
            {
                if(UsniAdaptor::app()->maintenanceManager->checkAccess() === false)
                {
                    UsniAdaptor::app()->user->logout(false);
                    UsniAdaptor::app()->cache->flush();
                    $url = UsniAdaptor::createUrl(UsniAdaptor::app()->maintenanceManager->url);
                    return $this->owner->redirect($url);
                }
            }
        }
        return true;
	}
}