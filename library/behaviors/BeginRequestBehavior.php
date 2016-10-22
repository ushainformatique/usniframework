<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\behaviors;

use usni\UsniAdaptor;
use yii\base\Behavior;
use yii\base\Application;
use yii\helpers\Url;
use usni\library\utils\ConfigurationUtil;
use usni\library\utils\CacheUtil;

/**
 * BeginRequestBehavior class file.
 * The methods would be used when onBeginRequest event is raised by the application.
 * 
 * @package usni\library\behaviors
 */
class BeginRequestBehavior extends Behavior
{
    /**
     * Attach events with this behavior.
     * @return array
     */
    public function events()
    {
        return [Application::EVENT_BEFORE_REQUEST => [$this, 'handleOnBeginRequest']];
    }
    
    /**
     * Event handler before request is processed.
     * @return void
     */
    public function handleOnBeginRequest()
    {
        $this->isRebuildInProgress();
        $this->checkInstallAndRedirect();
        $this->checkIfRequestWithoutCache();
        $this->checkIfClearCacheRequest();
    }

    /**
     * Check if rebuild is in progress
     */
    protected function isRebuildInProgress()
    {
        $currentUrl   = Url::to('');
        $homeUrl      = Url::home();
        if(UsniAdaptor::app()->installed === true
                && UsniAdaptor::app()->isRebuildInProgress() === true
                    && strpos($currentUrl, 'service/default/rebuild') === false)
        {
            if(strpos($currentUrl, 'backend') === false)
            {
                $url = UsniAdaptor::app()->getRequest()->baseUrl . '/backend/index.php';
            }
            else
            {
                $url = UsniAdaptor::app()->getRequest()->baseUrl . '/index.php';
            }
            if(UsniAdaptor::app()->urlManager->enablePrettyUrl === true)
            {
                $url .= '/service/default/rebuild';
            }
            else
            {
                $url .= '?' . UsniAdaptor::app()->urlManager->routeVar . '=service/default/rebuild';
            }
            UsniAdaptor::app()->getResponse()->redirect($url)->send();
            return;
        }
        elseif(UsniAdaptor::app()->isInstalled() === true
                && UsniAdaptor::app()->isRebuildInProgress() === false
                    && strpos($currentUrl, 'service/default/rebuild') !== false)
        {
            UsniAdaptor::app()->getResponse()->redirect($homeUrl)->send();
            return;
        }
    }

    /**
     * Checks if application is installed. If application is not installed and user tries to access
     * any other page, would be redirected to install index page.
     * @return void
     */
    protected function checkInstallAndRedirect()
    {
        $url        = Url::to('');
        $baseUrl    = Url::base(true);
        if(UsniAdaptor::app()->isInstalled() === false)
        {
            if(strpos($url, 'backend') === false)
            {
                $url = UsniAdaptor::app()->getRequest()->baseUrl . '/backend/index.php';
                UsniAdaptor::app()->getResponse()->redirect($url)->send();
                UsniAdaptor::app()->end(0);
            }
            if(strpos($url, 'install') === false)
            {
                if(UsniAdaptor::app()->urlManager->enablePrettyUrl === true)
                {
                    $url = $baseUrl . '/install/default';
                }
                else
                {
                    $url = $baseUrl . '?' . UsniAdaptor::app()->getUrlManager()->routeParam . '=install/default';
                }
                UsniAdaptor::app()->getResponse()->redirect($url)->send();
                UsniAdaptor::app()->end(0);
            }
        }
    }

    /**
     * Checks user requested the page without any cache.
     * @return void
     */
    protected function checkIfRequestWithoutCache()
    {
        $isCacheRequired = UsniAdaptor::getRequestParam('cache', 'true');
        if($isCacheRequired === 'false')
        {
            UsniAdaptor::app()->set('cache', new \yii\caching\DummyCache());
        }
    }

    /**
     * Checks if request is to clear the cache.
     * @return void
     */
    protected function checkIfClearCacheRequest()
    {
        $clearCache = UsniAdaptor::getRequestParam('clearCache', 'false');
        if($clearCache == 'true')
        {
            CacheUtil::clearCache();
        }
    }
}