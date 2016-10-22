<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\views;

use usni\library\modules\notification\models\NotificationTemplate;
use usni\UsniAdaptor;
use usni\library\modules\notification\utils\NotificationUtil;
use usni\library\modules\notification\components\PreviewActionColumn;
use usni\library\utils\ArrayUtil;
use yii\bootstrap\Modal;
/**
 * NotificationTemplateGridView class file
 * @package usni\library\modules\notification\views
 */
class NotificationTemplateGridView extends \usni\library\components\TranslatableGridView
{
    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        $columns = [
                        [
                            'attribute' => 'notifykey',
                        ],
                        [
                            'attribute' => 'type',
                            'filter'    => NotificationTemplate::getNotificationType()
                        ],
                        'subject',
                        [
                            'label'     => UsniAdaptor::t('notification',  'Layout'),
                            'attribute' => 'layout_id',
                            'value'     => [$this->model, 'getNotificationLayoutName']
                        ],
                        [
                            'class'     => PreviewActionColumn::className(),
                            'template'  => '{view} {update} {delete} {preview}'
                        ]
                   ];
        return $columns;
    }
    
    /**
     * @inheritdoc
     */
    protected function registerScripts()
    {
        $url = UsniAdaptor::createUrl('/notification/template/grid-preview');
        $editViewId = strtolower(UsniAdaptor::getObjectClassName($this->model)).'editview';
        NotificationUtil::registerGridPreviewScript($url, $editViewId, $this->getView());
    }
    
    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $content        = parent::renderContent();
        $content       .= $this->renderPreviewModal();
        return $content;
    }
    
    /**
     * Renders detailview modal.
     * @return string
     */
    protected function renderPreviewModal()
    {
        $options = ['id' => 'gridPreviewModal'];
        $options = ArrayUtil::merge($options, $this->getPreviewModalOptions());
        ob_start();
        Modal::begin($options);
        echo '';
        Modal::end();
        $output = ob_get_clean();
        return $output;
    }

    /**
     * @inheritdoc
     */
    protected function getPreviewModalOptions()
    {
        return ['size' => Modal::SIZE_LARGE];
    }
}
?>