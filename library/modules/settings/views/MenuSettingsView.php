<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\views;

use usni\library\utils\ButtonsUtil;
use usni\library\utils\FlashUtil;
use usni\UsniAdaptor;
use usni\library\extensions\bootstrap\views\UiBootstrapEditView;
use usni\library\widgets\UiSortableMultiSelectList;
use usni\library\components\UiActiveForm;
use usni\library\exceptions\MethodNotImplementedException;
/**
 * MenuSettingsView class file
 *
 * @package usni\library\modules\settings\views
 */
class MenuSettingsView extends UiBootstrapEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $elements = array(
            'sortOrder'       => array('type'                   => UiActiveForm::INPUT_WIDGET, 
                                       'class'                  => UiSortableMultiSelectList::className(),
                                       'htmlOptions'            => ['class' => 'form-control form-control-listbox'],
                                       'listItems'              => $this->getListItems(),
                                       'navigatorHtmlOptions'   => array('style' => 'padding-top:60px;'),
                                       'registerScript'         => true,
                                       'formId'                 => static::getFormId()
                                      ),
            'itemClass'       => array('type' => 'text'),
            'containerClass'  => array('type' => 'text')
        );

        $metadata = array(
            'elements'  => $elements,
            'buttons'   => array('save'   => ButtonsUtil::getSubmitButton(UsniAdaptor::t('application','Save')))
        );

        return $metadata;
    }

    /**
     * @inheritdoc
     */
    protected function renderTitle()
    {
        return UsniAdaptor::t('settings', 'Menu Settings');
    }

    /**
     * @inheritdoc
     */
    protected function renderFlashMessages()
    {
        return FlashUtil::render('menuSettingsSaved', 'alert alert-success');
    }

    /**
     * Gets menu list items.
     * @throws MethodNotImplementedException
     * @return array
     */
    protected function getListItems()
    {
        throw new MethodNotImplementedException(__METHOD__, __CLASS__);
    }
}
?>