<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\views;

use usni\UsniAdaptor;
use usni\library\components\UiGridView;
use usni\library\modules\settings\utils\ModuleSettingsActionColumn;
use usni\library\widgets\UiStatusDataColumn;
/**
 * ModuleSettingsView class file
 *
 * @package usni\library\modules\settings\views
 */
class ModuleSettingsView extends UiGridView
{
    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        $columns = [
            [
                'label'     => UsniAdaptor::t('application','Module'),
                'attribute' => 'id',
            ],
            [
                'attribute' => 'status',
                'class'     => UiStatusDataColumn::className()
            ],
            [
                'class'     => ModuleSettingsActionColumn::className(),
                'template'  => '{changestatus}'
            ],
        ];
        return $columns;
    }

    /**
     * @inheritdoc
     */
    protected function getTitle()
    {
        return UsniAdaptor::t('settings', 'Module Settings');
    }
    
    /**
     * @inheritdoc
     */
    protected function renderToolbar()
    {
        return null;
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