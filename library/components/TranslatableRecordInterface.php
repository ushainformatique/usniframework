<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

/**
 * TranslatableRecordInterface would be implemented by active records having translatable
 * content.
 * @package usni\library\components
 */
interface TranslatableRecordInterface
{
    /**
     * Get translatable attributes.
     * @return array
     */
    public static function getTranslatableAttributes();
}
