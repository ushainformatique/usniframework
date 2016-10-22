<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\exceptions;

use usni\UsniAdaptor;
/**
 * Missing Argument Exception.
 * @package usni\library\exceptions
 */
class MissingArgumentException extends \yii\base\Exception
{
    /**
     * Class constructor.
     * @param string  $argument Argument of the class.
     * @param string  $class    Class name.
     * @param string  $message  Exception message.
     * @param integer $code     Exception code.
     */
    public function __construct($argument, $class, $message=null, $code=0)
    {
        assert('is_string($argument)');
        $message = UsniAdaptor::t('application', 'Argument "{argument}" is missing in class "{class}"',
                                                        array('argument' => $argument, 'class' => $class));
        parent::__construct($message, $code);
    }
}
?>