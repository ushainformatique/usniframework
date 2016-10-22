<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\managers;

/**
 * TableManager class file
 * @package usni\library\managers
 */
class TableManager extends \usni\library\components\UiTableManager
{
    /**
     * @inheritdoc
     */
    protected static function getTableBuilderConfig()
    {
        return [
            ConfigurationTableBuilder::className(),
            SessionTableBuilder::className()
        ];
    }
}