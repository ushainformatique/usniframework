<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\settings\components;

use usni\library\components\BaseViewHelper;
/**
 * ViewHelper class file.
 *
 * @package usni\library\modules\settings\components
 */
class ViewHelper extends BaseViewHelper
{
    /**
     * Menu settings view
     * @var string 
     */
    public $menuSettingsView     = 'usni\library\modules\settings\views\MenuSettingsView';
    
    /**
     * Email settings view
     * @var string 
     */
    public $emailSettingsView     = 'usni\library\modules\settings\views\EmailSettingsView';
    
    /**
     * Site settings view
     * @var string 
     */
    public $siteSettingsView     = 'usni\library\modules\settings\views\SiteSettingsView';
    
    /**
     * Admin menu settings view
     * @var string 
     */
    public $adminMenuSettingsView     = 'usni\library\modules\settings\views\AdminMenuSettingsView';
}