<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\views;

use usni\library\extensions\bootstrap\views\UiBootstrapEditView;
use usni\library\components\UiHtml;
use usni\library\utils\ButtonsUtil;
use usni\library\modules\notification\models\NotificationTemplate;
use usni\library\modules\notification\utils\NotificationUtil;
use usni\UsniAdaptor;
use usni\library\components\UiActiveForm;
use marqu3s\summernote\Summernote;
/**
 * NotificationTemplateEditView class file
 * 
 * @package usni\library\modules\notification\views
 */
class NotificationTemplateEditView extends UiBootstrapEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $elements = [
                        'type'          => UiHtml::getFormSelectFieldOptionsWithNoSearch(NotificationTemplate::getNotificationType()),
                        'notifykey'     => ['type' => 'text'],
                        'subject'       => ['type' => 'text'],
                        'content'       => ['type' => UiActiveForm::INPUT_WIDGET, 'class' => Summernote::className()],
                        'layout_id'     => UiHtml::getFormSelectFieldOptionsWithNoSearch(NotificationUtil::getLayoutSelectOptions(), [], ['placeholder' => UiHtml::getDefaultPrompt()]),
                    ];

        $metadata = [
                        'elements' => $elements,
                        'buttons'  => self::getDefaultButtonsMetadata('notification/template/manage')
                    ];

        return $metadata;
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultButtonsMetadata($cancelUrl, $buttonId = 'savebutton')
    {
        return array(
            'preview'=> ButtonsUtil::getPreviewLinkElementData(),
            'save'   => ButtonsUtil::getSubmitButton(UsniAdaptor::t('application', 'Save')),
            'cancel' => ButtonsUtil::getCancelLinkElementData($cancelUrl)
        );
    }

    /**
     * @inheritdoc
     */
    protected function registerScripts()
    {
        parent::registerScripts();
        $url = UsniAdaptor::createUrl('/notification/template/preview');
        NotificationUtil::registerPreviewScript($url, $this->getId(), $this->getView());
    }

    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $content        = parent::renderContent();
        $modalContent   = UsniAdaptor::app()->controller->renderPartial('@usni/themes/bootstrap/views/layouts/_modalpreview', [], true);
        return $content . $modalContent;
    }
    
    /**
     * @inheritdoc
     */
    protected function resolveDefaultBrowseByAttribute()
    {
        return 'notifykey';
    }
}