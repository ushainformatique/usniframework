<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\components\UiHtml;
use usni\UsniAdaptor;
use usni\library\utils\ArrayUtil;

/**
 * LoginView class file.
 * @package usni\library\modules\users\views
 */
class LoginView extends \usni\library\views\UiView
{
    /**
     * Model associated with the view
     * @var LoginForm 
     */
    public $model;
    
    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $file           = UsniAdaptor::getAlias($this->getFile());
        return $this->getView()->renderPhpFile($file, array('model' => $this->model));
    }
    
    /**
     * Get login file
     * @return string
     */
    protected function getFile()
    {
        return '@usni/themes/bootstrap/views/users/_login.php';
    }
    
    /**
     * @inheritdoc
     */
    protected function registerScripts()
    {
        $modelHints = $this->model->attributeHints();
        $usernameHint = ArrayUtil::getValue($modelHints, 'username', null);
        if($usernameHint != null)
        {
            $id = UiHtml::getInputId($this->model, 'username');
            $script = "$('#{$id}').tooltip({'container':'body', 'trigger':'hover focus', 'placement':'right', 'title':'{$usernameHint}'});";
            $this->getView()->registerJs($script, \yii\web\View::POS_END);
        }
        $passwordHint = ArrayUtil::getValue($modelHints, 'password', null);
        if($passwordHint != null)
        {
            $id = UiHtml::getInputId($this->model, 'password');
            $script = "$('#{$id}').tooltip({'container':'body', 'trigger':'hover focus', 'placement':'right', 'title':'{$passwordHint}'});";
            $this->getView()->registerJs($script, \yii\web\View::POS_END);
        }
        
        $script             = "$('#login-form').on('beforeSubmit',
                                     function(event, jqXHR, settings)
                                     {
                                        var form = $(this);
                                        if(form.find('.has-error').length) {
                                                return false;
                                        }
                                        attachButtonLoader(form);
                                        return true;
                                     }
                                );";
        $this->getView()->registerJs($script);
    }
}
?>