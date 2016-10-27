<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\components;

use usni\library\components\BaseViewHelper;
/**
 * ViewHelper class file.
 *
 * @package usni\library\modules\users\components
 */
class ViewHelper extends BaseViewHelper
{
    /**
     * Login view.
     * @var string 
     */
    public $loginView     = 'usni\library\modules\users\views\LoginView';
    
    /**
     * User edit view.
     * @var string 
     */
    public $userEditView     = 'usni\library\modules\users\views\UserEditView';
}