<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\views;

use usni\library\extensions\bootstrap\views\UiTabbedEditView;
use usni\library\components\UiHtml;
use usni\library\utils\ButtonsUtil;
use usni\UsniAdaptor;
use usni\library\utils\FlashUtil;
use usni\library\modules\notification\utils\NotificationUtil;
/**
 * EmailSettingsView class file
 * @package usni\library\modules\settings\views
 */
class EmailSettingsView extends UiTabbedEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $elements = [
                        'fromName'              => ['type' => 'text'],
                        'fromAddress'           => ['type' => 'text'],
                        'replyToAddress'        => ['type' => 'text'],
                        //'pathToSendmail'        => array('type' => 'text'),
                        'sendingMethod'         => UiHtml::getFormSelectFieldOptionsWithNoSearch(NotificationUtil::getMailSendingMethod()),
                        'testEmailAddress'      => ['type' => 'text'],
                        'smtpHost'              => ['type' => 'text'],
                        'smtpPort'              => ['type' => 'text'],
                        'smtpUsername'          => ['type' => 'text'],
                        'smtpPassword'          => ['type' => 'password'],
                        //'smtpAuth'              => array('type' => 'checkbox'),
                        'sendTestMail'          => ['type' => 'checkbox'],
                        'testMode'              => ['type' => 'checkbox'],
                    ];

        $metadata = [
                        'elements'  => $elements,
                        'buttons'   => ['save'   => ButtonsUtil::getSubmitButton(UsniAdaptor::t('application', 'Save'))]
                    ];

        return $metadata;
    }

    /**
     * @inheritdoc
     */
    protected function renderTitle()
    {
        return UsniAdaptor::t('settings', 'Email Settings');
    }

    /**
     * Get tabs.
     * @return array
     * @Mayank Need to fix it.
     */
    protected function getTabs()
    {
        $tabs = array('userInfo'   => array('label'   => UsniAdaptor::t('application', 'General'),
                                            'content' => $this->renderTabElements('userInfo')),
                      /*'sendMail' => array('title'   => getLabel('notification', 'sendmail'),
                                            'content' => $this->renderTabElements('sendMail')),*/
                      'smtpMail'   => array('label'   => UsniAdaptor::t('notification', 'SMTP'),
                                            'content' => $this->renderTabElements('smtpMail'))
                      );
        return $tabs;
    }

    /**
     * Get tab elements map.
     * @return array
     * @Mayank Need to fix it.
     */
    protected function getTabElementsMap()
    {
        return array(
                        'userInfo'   => array('fromName', 'fromAddress', 'replyToAddress', 'sendingMethod', 'sendTestMail', 'testEmailAddress', 'testMode'),
                        //'sendMail' => array('pathToSendmail'),
                        //'smtpMail'   => array('smtpHost', 'smtpPort', 'smtpUsername', 'smtpPassword', 'smtpAuth')
                        'smtpMail'   => array('smtpHost', 'smtpPort', 'smtpUsername', 'smtpPassword')
                    );
    }

    /**
     * Renders flash messages.
     * @return string
     */
    protected function renderFlashMessages()
    {
        return FlashUtil::render(array('emailSettingsSaved', 'testEmailSent', 'smtpConfNotCorrect', 'testEmailNotProvided'),
                                 array('alert alert-success', 'alert alert-success', 'alert alert-danger', 'alert alert-danger'));
    }
    
    /**
     * @inheritdoc
     */
    protected function attributeOptions()
    {
        return array(
            'sendTestMail' => array(
                    'options' => [],
                    'horizontalCheckboxTemplate' => "<div class=\"checkbox checkbox-admin\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n</div>\n{error}"
            ),
            'testMode' => array(
                    'options' => [],
                    'horizontalCheckboxTemplate' => "<div class=\"checkbox checkbox-admin\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n</div>\n{error}"
            )
        );
    }
}
?>