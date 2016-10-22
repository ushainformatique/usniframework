<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\traits;

use usni\library\utils\TranslationUtil;
/**
 * EditViewTranslationTrait class file.
 * @package usni\library\traits
 */
trait EditViewTranslationTrait
{
    /**
     * @inheritdoc
     */
    public function afterModelSave($model)
    {
        if($this->action->id == 'create')
        {
            TranslationUtil::saveTranslatedModels($model);
        }
        return true;
    }
}
