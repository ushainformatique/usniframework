<?php
 /**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\UsniAdaptor;
use yii\helpers\Json;
/**
 * BulkScriptUtil class file. 
 * @package usni\library\utils
 */
class BulkScriptUtil
{
    /**
     * Register bulkDelete script.
     *
     * @param string  $url        Url to send ajax response.
     * @param integer $id         GridView id.
     */
    public static function registerBulkDeleteScript($url, $gridViewId, $view, $pjaxId)
    {
        $confirmation   = "if(!confirm(".  Json::encode(UsniAdaptor::t('application', 'Are you sure you want to perform bulk delete?')).")) return false;";
        $scriptBody     = self::renderScriptBody($url, $gridViewId, $confirmation, $pjaxId);
        $view->registerJs("$('body').on('click', '.multiple-delete',
                                    {$scriptBody});");
    }

    /**
     * Register bulk approve script.
     *
     * @param string  $url Url to send ajax response.
     * @param integer $id  GridView id.
     */
    public static function registerBulkApproveScript($url, $gridViewId, $btnClass, $view, $pjaxId)
    {
        $scriptBody = self::renderScriptBody($url, $gridViewId, $confirmation = '', $pjaxId);
        $view->registerJs("$('body').on('click', '.bulk-{$btnClass}', {$scriptBody});");
    }

    /**
     * Register bulk unapprove script.
     *
     * @param string  $url Url to send ajax response.
     * @param integer $id  GridView id.
     */
    public static function registerBulkUnApproveScript($url, $gridViewId, $btnClass, $view, $pjaxId)
    {
       $scriptBody = self::renderScriptBody($url, $gridViewId, $confirmation = '', $pjaxId);
       $view->registerJs("$('body').on('click', '.bulk-{$btnClass}', {$scriptBody});");
    }

    /**
     * Renders bulk script body
     * @param string $url.
     * @param string $gridViewId.
     * @param string $confirmation.
     * @return string
     */
    protected static function renderScriptBody($url, $gridViewId, $confirmation = '', $sourceId)
    {
        $error  = UsniAdaptor::t('application', 'Please select at least one record.');
        return "function()
                {
                    $confirmation
                    var idList = $('#{$gridViewId}').yiiGridView('getSelectedRows');
                    console.log(idList);
                    if(idList == '')
                    {
                        alert('{$error}');
                        return false;
                    }
                    $.ajax({
                            'type'     : 'GET',
                            'dataType' : 'html',
                            'url'      : '{$url}',
                            'data'     : {id:idList},
                            'beforeSend':function()
                                         {
                                            $.fn.attachLoader('#{$gridViewId}');
                                         },
                            'success'  : function(data)
                                         {
                                            $.pjax.reload({container:'#{$sourceId}', 'timeout':4000});
                                            $.fn.removeLoader('#{$gridViewId}');
                                         }
                          });
                    return false;
                }";
    }
}
?>
