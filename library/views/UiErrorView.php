<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\UsniAdaptor;
/**
 * UiErrorView class file
 * @package usni\library\views
 */
class UiErrorView extends UiView
{
    /**
     * Error associated with the view.
     * @var \Exception
     */
    protected $error;

    /**
     * Error info.
     * @var array
     */
    protected $errorInfo;

    /**
     * Class constructor.
     * @param \Exception $error
     * @param array $errorInfo
     */
    public function __construct($error, $errorInfo)
    {
        $this->error     = $error;
        $this->errorInfo = $errorInfo;
    }

    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $errorInfo = $this->errorInfo;
        if(YII_DEBUG)
        {
            echo $this->getView()->renderPhpFile($this->resolveExceptionFile(), array('exception' => $this->error,
                                                                                                    'handler' => UsniAdaptor::app()->errorHandler));
            exit;
        }
        else
        {
            return $this->getView()->renderPhpFile($this->resolveErrorFile(), array('name'    => $errorInfo['name'],
                                                                                                  'message' => $errorInfo['message'],
                                                                                                  'handler' => UsniAdaptor::app()->errorHandler));
        }
    }

    /**
     * Resolves error file.
     * @return string
     */
    protected function resolveErrorFile()
    {
        return UsniAdaptor::getAlias('@usni/themes/bootstrap/views/site/error.php');
    }

    /**
     * Resolves exception file.
     * @return string
     */
    protected function resolveExceptionFile()
    {
        return UsniAdaptor::getAlias('@usni/themes/bootstrap/views/site/exception.php');
    }
}
?>