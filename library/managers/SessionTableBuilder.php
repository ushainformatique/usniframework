<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl.html
 */
namespace usni\library\managers;

use usni\library\components\UiTableBuilder;
use yii\db\Schema;
use usni\UsniAdaptor;
/**
 * SessionTableBuilder class file.
 *
 * @package common\modules\cms\managers
 */
class SessionTableBuilder extends UiTableBuilder
{
    /**
     * @inheritdoc
     */
    protected function getTableSchema()
    {
        return [
            'id' => Schema::TYPE_STRING . '(40) PRIMARY KEY NOT NULL',
            'expire' => $this->integer(11),
            'data' => $this->binary()
        ];
    }
    
    /**
     * @inheritdoc
     */
    protected function getIndexes()
    {
        return [
                    ['idx_expire', 'expire', false],
               ];
    }
    
    /**
     * @inheritdoc
     */
    public function getTableName()
    {
        return UsniAdaptor::app()->db->tablePrefix.'session';
    }
}
