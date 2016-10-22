<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\exceptions;

use usni\UsniAdaptor;
use yii\base\Exception;
/**
 * Failed To Save Model Exception.
 * 
 * @package usni\library\exceptions
 */
class FailedToSaveModelException extends Exception
{
    /**
     * Class constructor.
     * @param string  $modelClass Model class name.
     * @param string  $message    Exception message.
     * @param integer $code       Exception code.
     */
    public function __construct($modelClass, $message=null, $code=0)
    {
        $message = UsniAdaptor::t('application', 'Failed to save model "{model}"', ['model' => $modelClass]);
        parent::__construct($message, $code);
    }
}