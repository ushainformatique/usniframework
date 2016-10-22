<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\select2;

use usni\library\web\UiAssetBundle;
/**
 * Asset bundle related to Eselect2 extension.
 *
 * @package usni\library\extensions\select2
 */
class Select2Asset extends UiAssetBundle
{
    public $sourcePath = '@usni/library/extensions/select2/assets';

    public $css = [
        'css/select2.css',
        'css/select2-bootstrap.css',
    ];

    public $js = ['js/select2.min.js'];
    public $depends = [
        'usni\library\assets\UiAdminAssetBundle',
    ];
}
