<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\library\extensions\bootstrap\views\UiBootstrapEditView;
use usni\library\utils\ButtonsUtil;
/**
 * UiBaseSearchView class file
 * @package usni\library\views
 */
abstract class UiBaseSearchView extends UiBootstrapEditView
{
    /**
     * Parent view.
     * @var string
     */
    protected $parentView;

    /**
     * Class constructor.
     *
     * @param array  $model
     * @param Widget $parentView
     */
    public function __construct($model, $parentView)
    {
        $this->parentView  = $parentView;
        parent::__construct($model);
    }

    /**
     * Resolve form view path.
     * @return string
     */
    public function resolveFormViewPath()
    {
        return '@usni/themes/bootstrap/views/site/_searchform';
    }

    /**
     * Registers script.
     * @return void
     */
    protected function registerScripts()
    {
        $this->getView()->registerJs("
            $('.search-action-button').click(function(){
                showHideToolbarContent('.search-form');
                return false;
            });
            ");
    }
    
    /**
     * Gets submit button metadata.
     * @return array
     */
    protected function getSubmitButton()
    {
        return [
                  'submit' => ButtonsUtil::getSubmitButton(UsniAdaptor::t('application', 'Search'))
               ];
    }
}
