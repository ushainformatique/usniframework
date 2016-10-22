<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for usni framework based front interface.
 * 
 * @see Advanced template provided by http://yiiframework.com for Yii2
 * @package usni\library\assets
 */
class UiFrontAssetBundle extends AssetBundle
{
    public $sourcePath = '@usni/library/web/assets';
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'usni\fontawesome\FontAwesomeAsset',
    ];
}
