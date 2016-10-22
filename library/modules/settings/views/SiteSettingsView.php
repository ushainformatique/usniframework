<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\views;

use usni\library\extensions\bootstrap\views\UiTabbedEditView;
use usni\library\utils\ButtonsUtil;
use usni\UsniAdaptor;
use usni\library\components\UiHtml;
use usni\library\utils\FlashUtil;
use usni\library\modules\install\components\InstallManager;
use usni\library\components\UiActiveForm;
use marqu3s\summernote\Summernote;
use usni\library\utils\FileUploadUtil;
/**
 * SiteSettingsView class file
 *
 * @package usni\library\modules\settings\views
 */
class SiteSettingsView extends UiTabbedEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $elements = [
                        'siteName'              => ['type' => 'text'],
                        'siteDescription'       => ['type' => 'textarea'],
                        '<hr/>',
                        'frontTheme'            => UiHtml::getFormSelectFieldOptions(InstallManager::getAvailableThemes()),
                        'metaKeywords'          => ['type' => 'textarea'],
                        'metaDescription'       => ['type' => 'textarea'],
                        'siteMaintenance'       => ['type' => 'checkbox'],
                        'customMaintenanceModeMessage' => ['type' => UiActiveForm::INPUT_WIDGET, 'class' => Summernote::className()],
                        $this->renderLogo(),
                        'logo'                  => ['type' => UiActiveForm::INPUT_FILE],
                        'tagline'               => ['type' => 'text'],
                        'logoAltText'           => ['type' => 'text']
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
        return UsniAdaptor::t('settings', 'Site Settings');
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
    protected function renderLogo()
    {
        if ($this->model->logo != null)
        {
            return FileUploadUtil::getThumbnailImage($this->model, 'logo');
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function getTabs()
    {
        return [     
                     'site'      => ['label'   => UsniAdaptor::t('application','Site'),
                                     'content' => $this->renderTabElements('site')],
                     'front'     => ['label' => UsniAdaptor::t('application','Front'),
                                     'content' => $this->renderTabElements('front')]
               ];
    }

    /**
     * @inheritdoc
     */
    protected function getTabElementsMap()
    {
        return [
                    'site'     => ['siteName', 'siteDescription', 'frontTheme', 'metaKeywords', 'metaDescription', 'siteMaintenance', 
                                   'customMaintenanceModeMessage'],
                     'front'   => ['logo', 'tagline', 'logoAltText']
               ];
    }

    /**
     * Renders tab element.
     * @param string $tab
     * @param string $name
     * @return string
     */
    protected function renderTabElement($tab, $name)
    {
        $elementsOutputData = $this->getElementsOutputData();
        if($name == 'logo')
        {
            return $this->renderLogo() . $elementsOutputData[$name];
        }
        return $elementsOutputData[$name];
    }

    /**
     * @inheritdoc
     */
    protected function renderFlashMessages()
    {
        return FlashUtil::render('siteSettingsSaved', 'alert alert-success');
    }

    /**
     * @inheritdoc
     */
    protected function attributeTemplates()
    {
        return array(
            'customMaintenanceModeMessage' => "<div class='form-group site-maintenance-msg{errorClass}'>{label}<div class='col-xs-8 input-group'>{input}{hint}</div></div>"
        );
    }

    /**
     * @inheritdoc
     */
    protected function registerScripts()
    {
        parent::registerScripts();
        UsniAdaptor::app()->getView()->registerJs('sitemaintenancescript', "
                                                      $('#SiteSettingsForm-siteMaintenance').click(function () {
                                                                    if ($(this).prop('checked') === true) {
                                                                        $('.site-maintenance-msg').show();
                                                                    } else {
                                                                        $('.site-maintenance-msg').hide();
                                                                    }

                                                                    });

                                                                                ");
    }
    
    /**
     * @inheritdoc
     */
    protected function attributeOptions()
    {
        $horizontalCssClasses   = $this->getHorizontalCssClasses();
        $horizontalCssClasses['wrapper'] = '';
        $horizontalCssClasses['label']   = '';
        return [
                 'siteMaintenance' => [
                                        'labelOptions'  => array(),
                                        'inputOptions'  => [],
                                        'horizontalCheckboxTemplate' => '<div class="col-xs-12"><div class="checkbox checkbox-success"><label>{input}   ' . UsniAdaptor::t('settings', 'Maintenance mode') . '</label></div></div>',
                                        'horizontalCssClasses' => $horizontalCssClasses
                                      ]
               ];
    }
}