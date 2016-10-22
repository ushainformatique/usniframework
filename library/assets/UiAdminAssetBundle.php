<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for usni framework based admin panel.
 * 
 * @see Advanced template provided by http://yiiframework.com for Yii2
 * @package usni\library\assets
 */
class UiAdminAssetBundle extends AssetBundle
{
    public $sourcePath = '@usni/library/web/assets';
    public $css = [
        'css/admin-theme.css',
        'css/styles.css',
        "//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=latin,cyrillic-ext"
    ];
    public $js = [
        'js/application.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'usni\fontawesome\FontAwesomeAsset',
    ];
}
