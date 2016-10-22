<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

/**
 * Base active record class for the model that needs translation.
 * 
 * @package usni\library\components
 */
abstract class TranslatableActiveRecord extends UiSecuredActiveRecord implements TranslatableRecordInterface
{
    use \usni\library\traits\TranslationTrait;
    
    /**
     * @inheritdoc
     */
    public static function getTranslatableAttributes()
    {
        return [];
    }
}