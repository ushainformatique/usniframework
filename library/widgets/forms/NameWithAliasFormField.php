<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\widgets\forms;

use usni\library\components\UiHtml;
/**
 * NameWithAliasFormField class file
 * @package usni\library\widgets\forms
 */
class NameWithAliasFormField extends \yii\widgets\InputWidget
{
    /**
     * Alias id associated with the name field. If not null than calculated based on the name value.
     * @var string
     */
    public $aliasId = null;

    /**
     * Render name field using alias.
     * @return void
     */
    public function run()
    {
        $this->aliasId      = UiHtml::getInputId($this->model, 'alias');
        $this->options['onkeyup'] = 'javascript:getAlias($(this).attr("id"), "' . $this->aliasId . '")';
        $this->options['class']   = 'form-control';
        echo UiHtml::activeTextInput($this->model, $this->attribute, $this->options);
    }
}
?>