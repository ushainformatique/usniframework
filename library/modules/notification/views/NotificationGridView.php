<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\views;

use usni\library\components\UiGridView;
use usni\library\modules\notification\utils\NotificationUtil;
use usni\library\modules\notification\components\EmailPriorityDataColumn;
use usni\UsniAdaptor;
use usni\library\modules\notification\components\NotificationStatusDataColumn;
/**
 * NotificationGridView class file
 * @package usni\library\modules\notification\views
 */
class NotificationGridView extends UiGridView
{
    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        $columns = [
                        [
                            'attribute' => 'type',
                            'value'     => [$this->model, 'getTypeDisplayLabel'],
                        ],
                        'modulename',
                        [
                            'attribute' => 'status',
                            'class'     => NotificationStatusDataColumn::className(),
                            'filter'    => NotificationUtil::getStatusListData()
                        ],
                        [
                            'attribute' => 'priority',
                            'class'     => EmailPriorityDataColumn::className(),
                            'filter'    => NotificationUtil::getPriorityListData()
                        ],
                        [
                            'attribute' => 'senddatetime',
                            'value'     => [$this->model, 'getSendDateTime']
                        ],
                        [
                            'label'     => UsniAdaptor::t('application', 'Message'),
                            'attribute' => 'data',
                            'value'     => [$this->model, 'getNotificationMessage'],
                            'format'    => 'html'
                        ]
                   ];
        return $columns;
    }
    
    /**
     * @inheritdoc
     */
    protected static function getActionToolbarOptions() {
        $toolbar                    = parent::getActionToolbarOptions();
        $toolbar['showBulkEdit']    = false;
        $toolbar['showBulkDelete']  = false;
        $toolbar['showCreate']      = false;
        return $toolbar;
    }
    
    /**
     * @inheritdoc
     */
    protected function renderCheckboxColumn()
    {
        return false;
    }
}
?>