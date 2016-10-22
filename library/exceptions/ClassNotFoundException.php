<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\exceptions;

use usni\UsniAdaptor;
/**
 * The exception would be raised in case class in not existing in the system.
 * @package usni\library\exceptions
 */
class ClassNotFoundException extends \yii\base\Exception
{
    /**
     * Class constructor.
     * @param string  $modelClass Model class name.
     * @param string  $message    Exception message.
     * @param integer $code       Exception code.
     */
    public function __construct($modelClass, $message=null, $code=0)
    {
        $message = UsniAdaptor::t('application', '"{model}" is missing', array('model' => $modelClass));
        parent::__construct($message, $code);
    }
}
?>