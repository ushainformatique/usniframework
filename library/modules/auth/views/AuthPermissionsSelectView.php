<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\views;

use usni\library\views\UiView;
use usni\UsniAdaptor;
use usni\library\modules\auth\models\AuthAssignment;
use usni\library\components\UiHtml;
use yii\helpers\Inflector;
use usni\library\modules\auth\managers\AuthManager;
/**
 * AuthPermissionsSelectView class file.
 * 
 * @package usni\library\modules\auth\views
 */
class AuthPermissionsSelectView extends UiView
{
    /**
     * Model associated with the view
     * @var Model   
     */
    protected $model;

    /**
     * Class constructor.
     * @param Model $model
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Renders content.
     * @return string
     */
    protected function renderContent()
    {
        $content          = null;
        $selectAllLabel   = UsniAdaptor::t('application', 'Select All');
        $modulePermissions = $this->model->permissions;
        $list = null;
        $isAllPermissionsSelected = true;
        foreach($modulePermissions as $moduleId => $permissions)
        {
            $count = AuthAssignment::find()
                     ->where('module = :module AND identity_name = :iname AND identity_type = :it', 
                             [':iname' => $this->model->authIdentity->getAuthName(),
                              ':it' => $this->model->authIdentity->getAuthType(),
                              ':module' => $moduleId])
                     ->count();
            
            $permissionCount = AuthManager::getModulePermissionCount($moduleId);
            if($count == 0)
            {
                $isAllPermissionsSelected = false;
                break;
            }
            elseif($count != $permissionCount)
            {
                $isAllPermissionsSelected = false;
                break;
            }
        }
        if($isAllPermissionsSelected)
        {
            $checked = true;
        }
        else
        {
            $checked = false;
        }
        $content         .= UiHtml::checkbox('selectAll',
                                                  $checked,
                                                  array('label' => '<span class="selectLabel">' . $selectAllLabel . '</span>',
                                                        'labelOptions' => array('class' => 'checkbox-inline selectAllCheckBoxLabel'),
                                                        'id' => 'selectAllChk'));
        foreach($modulePermissions as $moduleId => $permissionSet)
        {
            $moduleString = Inflector::camel2words(ucfirst($moduleId));
            $permissionContent = UiHtml::tag('legend', $moduleString);
            ksort($permissionSet);
            foreach($permissionSet as $resource => $permissions)
            {
                if(strpos($resource, 'Module') === false)
                {
                    $resourceString = Inflector::camel2words($resource);
                    $permissionContent .= UiHtml::tag('h4', $resourceString);
                }
                //ksort($permissions);
                $permissionContent .= UiHtml::activeCheckBoxList($this->model,
                                                            'authAssignments',
                                                            $permissions,
                                                            array('item' => [$this, 'getPermissionCheckBoxItem'],
                                                                  'unselect' => null,
                                                                  'id' => strtolower($resource)));
                $permissionContent .= '<br/>';
            }
            $list .= UiHtml::tag('div', $permissionContent, array('class' => 'modulePermissionsContainer'));
        }
        $content .= UiHtml::panelBody($list);
        return $content;
    }
    
    /**
     * Get the permission checkbox html.
     * 
     * The data would be as
     * [permissions] => Array
        (
            [auth] => Array
                (
                    [access.auth] => Access Tab
                    [auth.managepermissions] => Manage Permissions
                )
     * )
     * @see BaseHtml::checkboxList for params description
     */
    public function getPermissionCheckBoxItem($index, $label, $name, $checked, $value)
    {
        $baseId     = UiHtml::getInputId($this->model, 'authAssignments');
        $inputId    = $baseId . '-' . $value;
        $checkbox   = UiHtml::checkbox($name, $checked, ['class' => 'authitem', 'value' => $value, 'id' => $inputId]);
        $output     = UiHtml::label($checkbox . $label, $inputId, ['class' => 'checkbox-inline authpermissioncheckbox']);
        return $output;
    }
}
?>
