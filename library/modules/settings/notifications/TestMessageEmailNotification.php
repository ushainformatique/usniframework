<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\notifications;

use usni\library\components\UiEmailNotification;
use usni\UsniAdaptor;
use usni\library\modules\notification\models\Notification;
/**
 * TestMessageEmailNotification class file.
 * 
 * @package usni\library\modules\settings\notifications
 */
class TestMessageEmailNotification extends UiEmailNotification
{
    /**
     * @inheritdoc
     */
    public function getKey()
    {
        return 'testmessage';
    }

    /**
     * @inheritdoc
     */
    public function getModuleName()
    {
        return 'settings';
    }

    /**
     * @return int
     */
    public function getDeliveryPriority()
    {
        return Notification::PRIORITY_HIGH;
    }

    /**
     * @inheritdoc
     */
    protected function getTemplateData()
    {
        return array('{{message}}' => UsniAdaptor::t('settings', 'This is a test message'),
                     '{{appname}}'  => UsniAdaptor::app()->name);
    }

    /**
     * @inheritdoc
     */
    protected function getLayoutData($data)
    {
        return array('{{####content####}}' => $data['templateContent']);
    }
}