<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;
/**
 * UiListBaseSearchView class file
 *
 * @package usni.library.views
 */
abstract class UiListBaseSearchView extends UiBaseSearchView
{
    /**
     * Registers script.
     * @return void
     */
    protected function registerScripts()
    {
        $parentViewId = $this->parentViewId;
        UsniAdaptor::app()->clientScript->registerScript('listsearch', "
            $('.search-button').click(function(){
                $('.search-form').toggle();
                return false;
            });
            $('.search-form form').submit(function(){
                $.fn.yiiListView.update('{$parentViewId}', {
                    data: $(this).serialize()
                    });
                $('.search-form form').find('.form-group').removeClass('has-success');
                return false;
            });
            ");
    }
}