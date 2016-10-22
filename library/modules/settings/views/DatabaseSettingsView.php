<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\views;

use usni\library\extensions\bootstrap\views\UiBootstrapEditView;
use usni\library\utils\ButtonsUtil;
use usni\UsniAdaptor;
use usni\library\utils\FlashUtil;
/**
 * DatabaseSettingsView class file
 * 
 * @package usni\library\modules\settings\views
 */
class DatabaseSettingsView extends UiBootstrapEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $elements = [
                        'enableSchemaCache'         => ['type' => 'checkbox'],
                        'schemaCachingDuration'     => ['type' => 'text'],
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
        return UsniAdaptor::t('settings', 'Database Settings');
    }

    /**
     * @inheritdoc
     */
    protected function renderFlashMessages()
    {
        return FlashUtil::render('dbSettingsSaved', 'alert alert-success');
    }
    
    /**
     * @inheritdoc
     */
    protected function attributeOptions()
    {
        return array(
            'enableSchemaCache' => array(
                    'options' => [],
                    'horizontalCheckboxTemplate' => "<div class=\"checkbox checkbox-admin\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n</div>\n{error}"
            )
        );
    }
}