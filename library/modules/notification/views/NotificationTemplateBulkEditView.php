<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\views;

use usni\library\extensions\bootstrap\views\UiBootstrapBulkEditView;
use usni\library\components\UiHtml;
use usni\library\modules\notification\models\NotificationTemplate;
use usni\UsniAdaptor;
/**
 * NotificationTemplateBulkEditView class file
 * @package usni\library\modules\notification\views
 */
class NotificationTemplateBulkEditView extends UiBootstrapBulkEditView
{
     /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $elements = [
                        'type'      => UiHtml::getFormSelectFieldOptionsWithNoSearch(NotificationTemplate::getNotificationType()),
                    ];

        $metadata = [
                        'elements'      => $elements,
                        'buttons'       => $this->getSubmitButton()
                    ];

        return $metadata;
    }

    /**
     * @inheritdoc
     */
    protected function getTitle()
    {
        return UsniAdaptor::t('notification', 'Notification Template Bulk Edit');
    }

}
