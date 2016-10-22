<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\UsniAdaptor;
use yii\web\View;
use usni\library\components\UiHtml;
use usni\library\extensions\select2\ESelect2;
use usni\library\utils\ArrayUtil;
/**
 * Browse model view for admin panel.
 *
 * @package usni\library\views
 */
class UiBrowseModelView extends UiView
{
    /**
     * Selected edit model.
     * @var ActiveRecord|array
     */
    public $model;
    /**
     * Selected attribute to be displayed in the dropdown.
     * @var string
     */
    public $attribute;
    /**
     * Should render owner created model for browse only. For example show all the posts of the owner.
     * @var bool
     */
    public $shouldRenderOwnerCreatedModelsForBrowse;

    /**
     * @return string
     */
    protected function renderContent()
    {
        $content = $this->renderDropDown();
        if($content == null)
        {
            return null;
        }
        $file = UsniAdaptor::getAlias('@usni/themes/bootstrap/views/site/_browse.php');
        return $this->getView()->renderPhpFile($file, array('content' => $content));
    }

    /**
     * Renders dropdown.
     * @return string
     */
    protected function renderDropDown()
    {
        $data = $this->resolveDropdownData();
        $this->unsetNotAllowed($data);
        if(empty($data))
        {
            return null;
        }
        if(YII_ENV == YII_ENV_TEST)
        {
            return UiHtml::dropDownList('browse', '', $data, ['prompt' => UiHtml::getDefaultPrompt(), 'id' => 'viewbrowsemodels']);
        }
        return ESelect2::widget(['data'          => $data,
                                 'select2Options'=> [],
                                 'options'       => ['placeholder' => UiHtml::getDefaultPrompt()],
                                 'name'          => 'browse',
                                 'id'            => 'viewbrowsemodels'
                                ]);
    }
    
    /**
     * Unset not allowed data
     * @param array $data
     * @return void
     */
    protected function unsetNotAllowed(& $data)
    {
        unset($data[$_GET['id']]);
    }

    /**
     * Registers script for the edit page.
     * @return void
     */
    protected function registerScripts()
    {
        $route      = UsniAdaptor::app()->controller->getRoute();
        $url        = UsniAdaptor::createUrl($route);
        $script     = "$('#viewbrowsemodels').on('change', function(){
                        var url = '{$url}' + '?id='+$(this).val();
                        window.location.href = url;
                       })";
        UsniAdaptor::app()->getView()->registerJs($script, View::POS_END);
    }

    /**
     * Resolve dropdown data.
     * @return array
     */
    protected function resolveDropdownData()
    {
        $modelClassName  = get_class($this->model);
        $models          = $modelClassName::find()->orderBy(['id' => SORT_ASC])->all();
        $filteredModels  = array();
        foreach($models as $value)
        {
            if($this->shouldRenderOwnerCreatedModelsForBrowse)
            {
                if($value['id'] != $this->model->id && $value['created_by'] == $this->model->created_by)
                {
                    $filteredModels[] = $value;
                }
            }
            else
            {
                if($value['id'] != $this->model->id)
                {
                    $filteredModels[] = $value;
                }
            }
        }
        return ArrayUtil::map($filteredModels, 'id', $this->attribute);
    }
}
?>