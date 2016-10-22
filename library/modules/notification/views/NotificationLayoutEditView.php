<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\views;

use usni\library\extensions\bootstrap\views\UiBootstrapEditView;
use usni\library\utils\ButtonsUtil;
use usni\library\components\UiActiveForm;
use marqu3s\summernote\Summernote;
/**
 * NotificationLayoutEditView class file
 * @package usni\library\modules\notification\views
 */
class NotificationLayoutEditView extends UiBootstrapEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $elements = [
                        'name'      => ['type' => 'text'],
                        'content'   => ['type' => UiActiveForm::INPUT_WIDGET, 'class' => Summernote::className()],
                    ];

        $metadata = [
                        'elements' => $elements,
                        'buttons'  => ButtonsUtil::getDefaultButtonsMetadata('notification/layout/manage')
                    ];

        return $metadata;
    }
}
?>