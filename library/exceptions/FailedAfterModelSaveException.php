<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\exceptions;

use usni\UsniAdaptor;

/**
 * Failed After Model Save Exception.
 * @package usni\library\exceptions
 */
class FailedAfterModelSaveException extends \yii\db\Exception
{
    /**
     * Class constructor.
     * @param string  $modelClass Model class name.
     */
    public function __construct($modelClass)
    {
        $message = UsniAdaptor::t('application', 'Failed after saving model "{model}"', ['model' => $modelClass]);
        parent::__construct($message);
    }
}

?>
