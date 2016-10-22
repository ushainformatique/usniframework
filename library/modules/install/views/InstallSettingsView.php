<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\install\views;

use usni\library\extensions\bootstrap\views\UiTabbedEditView;
use usni\UsniAdaptor;
use usni\library\components\UiHtml;
use usni\library\modules\install\components\InstallManager;
use usni\library\utils\ButtonsUtil;
use usni\library\utils\TimezoneUtil;
use usni\library\components\UiActiveForm;
/**
 * InstallSettingsView class file.
 * @package usni\library\modules\install\views
 */
class InstallSettingsView extends UiTabbedEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $siteConfiguration          = UiHtml::panelTitle(UsniAdaptor::t('install', 'Main Configuration'));
        $dbConfiguration            = UiHtml::panelTitle(UsniAdaptor::t('install', 'Database Configuration'));
        $elements = [
                        $siteConfiguration,
                        'siteName'        => ['type' => 'text'],
                        'siteDescription' => ['type' => 'textarea'],
                        'superUsername'   => ['type' => 'text'],
                        'superEmail'      => ['type' => 'text'],
                        'superPassword'   => ['type' => 'password'],
                        'environment'     => UiHtml::getFormSelectFieldOptions(InstallManager::getEnvironments()),
                        'frontTheme'      => UiHtml::getFormSelectFieldOptions(InstallManager::getAvailableThemes()),
                        $dbConfiguration,
                        'dbAdminUsername' => ['type' => 'text'],
                        'dbAdminPassword' => ['type' => 'password'],
                        'dbHost'          => ['type' => 'text'],
                        'dbPort'          => ['type' => 'text'],
                        'dbName'          => ['type' => 'text'],
                        'dbUsername'      => ['type' => 'text'],
                        'timezone'        => UiHtml::getFormSelectFieldOptions(TimezoneUtil::getTimezoneSelectOptions(),
                                                                                   [], ['placeholder' => UiHtml::getDefaultPrompt()]),
                        'logo'            => ['type' => UiActiveForm::INPUT_FILE, 'visible' => 'true'],
                        'dbPassword'      => ['type' => 'password'],
                        'demoData'        => ['type' => 'checkbox']
                    ];
        $metadata = [
                        'elements'        => $elements,
                        'buttons'         => ButtonsUtil::getDefaultButtonsMetadata('install/default/check-system')
                    ];
        return $metadata;
    }

    /**
     * @inheritdoc
     */
    protected function buttonOptions()
    {
        return [
                 'install' => ['class' => 'btn btn-success'],
                 'cancel'  => ['class' => 'btn btn-default']
               ];
    }

    /**
     * @inheritdoc
     */
    protected function renderTitle()
    {
        return UsniAdaptor::t('install', 'System Settings');
    }

    /**
     * @inheritdoc
     */
    protected function attributeOptions()
    {
        $horizontalCssClasses   = $this->getHorizontalCssClasses();
        $horizontalCssClasses['wrapper'] = '';
        $horizontalCssClasses['label']   = '';
        return array(
            'demoData' => array(
                    'labelOptions'  => array(),
                    'inputOptions'  => [],
                    'horizontalCheckboxTemplate' => '<div class="col-xs-12"><div class="checkbox checkbox-success"><label>{input}   ' . UsniAdaptor::t('install', 'Install Demo Data') . '</label></div></div>',
                    'horizontalCssClasses' => $horizontalCssClasses
            )
        );
    }
    
    /**
     * @inheritdoc
     */
    protected function getTabElementsMap() 
    {
        return [
                    'site'     => ['siteName', 'siteDescription', 'superUsername', 'superEmail', 'superPassword', 'environment', 'frontTheme', 'timezone', 'logo', 'demoData'],
                    'database'   => ['dbAdminUsername', 'dbAdminPassword', 'dbHost', 'dbPort', 'dbName', 'dbUsername', 'dbPassword']
               ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getTabs() 
    {
        return [     
                     'site'      => ['label'   => UsniAdaptor::t('application','Site'),
                                     'content' => $this->renderTabElements('site')],
                     'database'  => ['label' => UsniAdaptor::t('application','Database'),
                                     'content' => $this->renderTabElements('database')]
               ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getDefaultAttributeOptions()
    {
        $options = parent::getDefaultAttributeOptions();
        $options['errorOptions'] = ['encode' => false];
        return $options;
    }
    
    /**
     * @inheritdoc
     */
    public function isMultiPartFormData()
    {
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function resolveFormViewPath()
    {
        return '@usni/themes/bootstrap/views/install/_form';
    }
    
    /**
     * @inheritdoc
     */
    public function renderCallOut()
    {
        return '<div class="bs-callout bs-callout-info fade in">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <h5>Instructions</h5>
                    <ul>
                        <li>' . UsniAdaptor::t('install', 'Please take up the backup of database if existing database is used') . '</li>
                        <li>' . UsniAdaptor::t('install', 'Populate database admin username and password if you are using a new database') . '</li>
                    </ul>
                </div>';
    }
    
    /**
     * @inheritdoc
     */
    protected function renderBegin()
    {
        return $this->getHeaderContent() . parent::renderBegin();
    }
    
    /**
     * Get header content
     * @return string
     */
    protected function getHeaderContent()
    {
        return '<div class="page-header">
                            <div class="page-title">
                                <h3>' . UsniAdaptor::t('application', '{appname} Installation', ['appname' => UsniAdaptor::app()->displayName]) . '</h3>
                            </div>
                        </div>';
    }
}
?>