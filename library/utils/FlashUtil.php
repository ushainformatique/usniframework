<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\UsniAdaptor;
use usni\library\components\UiHtml;
/**
 * Helper class for rendering flash message in the system.
 * 
 * @package usni\library\utils
 */
class FlashUtil
{
    /**
     * Get flash message.
     * @param string $key Flash key.
     * @param string $params Params.
     * @return string
     */
    public static function getMessage($key, $params = array())
    {
        $messages = static::getMessages();
        if (isset($messages[$key]))
        {
            return $messages[$key];
        }
        else
        {
            return UsniAdaptor::t('application', '(not set)');
        }
    }

    /**
     * Renders flash message. In case $keys is array, the class at the index for the key is used to fetch the html options.
     *
     * @param mixed $keys.
     * @param mixed $classes.
     * @return mixed
     */
    public static function render($keys, $classes = 'alert alert-success')
    {
        assert('is_string($keys) || is_array($keys)');
        assert('is_string($classes) || is_array($classes)');
        if (is_string($keys))
        {
            return self::resolveMessage($keys, $classes);
        }
        else if (is_array($keys))
        {
            $message = null;
            foreach ($keys as $index => $flashKey)
            {
                $message .= self::resolveMessage($flashKey, $classes[$index]);
            }
            return $message;
        }
        return null;
    }

    /**
     * Resolves message.
     * @param string $key   Flash key.
     * @param string $class Class name.
     * @return null
     * @throws exception yii\web\HttpException.
     */
    private static function resolveMessage($key, $class)
    {
        assert('is_string($key) || is_array($key)');
        assert('is_string($class) || is_array($class)');
        if (UsniAdaptor::app()->session->hasFlash($key))
        {
            if (is_string($class))
            {
                return UiHtml::tag('div', UsniAdaptor::app()->session->getFlash($key), ['class' => $class]);
            }
            else
            {
                throw new \yii\web\HttpException(500, UsniAdaptor::t('application', 'Invalid css class passed for the flash message with key as {key}',
                                        array('{key}' => $key)));
            }
        }
        return null;
    }
    
    /**
     * Set the flash message.
     * @param string $key       Key of the flash message.
     * @param string $flashMessage Module key.
     * @return void
     */
    public static function setMessage($key, $flashMessage)
    {
        UsniAdaptor::app()->getSession()->setFlash($key, $flashMessage);
    }
}