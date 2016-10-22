<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\extensions\bootstrap\views\UiBootstrapEditView;
use usni\library\utils\ButtonsUtil;
use usni\UsniAdaptor;
use usni\library\utils\FlashUtil;
use usni\library\modules\users\models\User;
/**
 * ChangePasswordView class file.
 *
 * @package usni\library\modules\users\views
 */
class ChangePasswordView extends UiBootstrapEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $elements = array(
            'newPassword'     => array('type' => 'password'),
            'confirmPassword' => array('type' => 'password')
        );
        $metadata = array(
            'elements' => $elements,
            'buttons'   => ButtonsUtil::getDefaultButtonsMetadata($this->getButtonUrl())
        );
        return $metadata;
    }

    /**
     * @inheritdoc
     */
    protected function renderTitle()
    {
        $user = User::findOne($_GET['id']);
        return UsniAdaptor::t('users', 'Change Password') . '(' . $user->username . ')';
    }

    /**
     * @inheritdoc
     */
    protected function renderFlashMessages()
    {
        $output  = FlashUtil::render('changepassword', 'alert alert-success');
        $output .= FlashUtil::render('passwordinstructions', 'alert alert-warning');
        return $output;
    }

    /**
     * Get button url
     * @return string
     */
    protected function getButtonUrl()
    {
        return 'users/default/manage';
    }
}
?>