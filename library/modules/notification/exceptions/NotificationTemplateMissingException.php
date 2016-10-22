<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
/**
 * Notification Template Missing Exception.
 *
 * @package usni.library.modules.notification.exceptions
 */
class NotificationTemplateMissingException extends CException
{

    /**
     * Class constructor.
     * @param string  $modelClass Model class name.
     * @param string  $message    Exception message.
     * @param integer $code       Exception code.
     */
    public function __construct ($key, $message = null, $code = 0)
    {
        $message = getLabel('notification', 'templateMissing', array('{key}' => $key));
        parent::__construct($message, $code);
    }
}
