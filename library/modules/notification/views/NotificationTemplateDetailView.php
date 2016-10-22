<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\views;

use usni\library\views\UiDetailView;
/**
 * NotificationTemplateDetailView class file.
 * @package usni\library\modules\notification\views
 */
class NotificationTemplateDetailView extends UiDetailView
{
    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        return [
                    'type',
                    'notifykey',
                    'subject',
                    [
                      'attribute' => 'content', 'format' => 'raw'  
                    ],
               ];
    }

    /**
     * @inheritdoc
     */
    protected function getTitle()
    {
        return $this->model->subject;
    }
    
    /**
     * @inheritdoc
     */
    protected function resolveDefaultBrowseByAttribute()
    {
        return 'notifykey';
    }
}
?>