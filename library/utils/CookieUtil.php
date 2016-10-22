<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\UsniAdaptor;
/**
 * Class consisting of utility functions related to cookie.
 * 
 * @package usni\library\utils
 */
class CookieUtil
{
    /**
     * Get value of the cookie.
     * @param string $cookieName
     * @return Cookie the cookie with the specified name. Null if the named cookie does not exist.
     */
    public static function getValue($cookieName)
    {
        return UsniAdaptor::app()->getRequest()->getCookies()->get($cookieName);
    }
    
    /**
     * Remove cookie.
     * @param string $cookieName
     * @return void
     */
    public static function remove($cookieName)
    {
        UsniAdaptor::app()->getResponse()->getCookies()->remove($cookieName);
    }
    
    /**
     * Remove all cookies.
     * @return void
     */
    public static function removeAllCookies()
    {
        UsniAdaptor::app()->getResponse()->getCookies()->removeAll();
    }
}