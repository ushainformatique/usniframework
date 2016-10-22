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
/**
 * SettingsView class file
 * @package usni\library\modules\users\views
 */
class SettingsView extends UiBootstrapEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $elements = [
                        'passwordTokenExpiry'     => ['type' => 'text'],
                    ];

        $metadata = [
                        'elements'  => $elements,
                        'buttons'   => ['save'   => ButtonsUtil::getSubmitButton(UsniAdaptor::t('application','Save'))]
                    ];

        return $metadata;
    }

    /**
     * @inheritdoc
     */
    protected function renderTitle()
    {
        return UsniAdaptor::t('users', 'User Settings');
    }

    /**
     * @inheritdoc
     */
    protected function renderFlashMessages()
    {
        return FlashUtil::render('userSettingsSaved', 'alert alert-success');
    }
}
?>