<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

/**
 * UiTableManager class file.
 * 
 * @package usni\library\modules\auth\managers
 */
abstract class UiTableManager extends \yii\base\Component
{
    /**
     * Build module tables
     */
    public function buildTables()
    {
        $config = static::getTableBuilderConfig();
        try
        {
            foreach ($config as $class)
            {
                $instance = new $class();
                $instance->buildTable();
            }
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }
    
    /**
     * Get complete database sql
     * @return string
     */
    public function getTablesSql()
    {
        $sqlStr   = null;
        $config = static::getTableBuilderConfig();
        try
        {
            foreach ($config as $class)
            {
                $sqls     = [];
                $instance = new $class();
                $sqls[]   = $instance->getTableSql();
                $indexSql = $instance->getIndexSql();
                if($indexSql != null)
                {
                    $sqls[]   = $indexSql;
                }
                $trSql    = $instance->getTranslatedTableSql();
                if($trSql != null)
                {
                    $sqls[]   = $trSql;
                }
                if(!empty($sqls))
                {
                    $sqlStr   .= implode(';', $sqls);
                }
            }
            return $sqlStr;
        }
        catch (\Exception $e)
        {
            throw $e;
        }
        return null;
    }
    
    /**
     * Get table builder config.
     * @return array
     */
    protected static function getTableBuilderConfig()
    {
        throw new \usni\library\exceptions\MethodNotImplementedException(__METHOD__, __CLASS__);
    }
    
    /**
     * Drop tables
     * @return void
     */
    public function dropTables()
    {
        $config = static::getTableBuilderConfig();
        foreach ($config as $class)
        {
            $instance = new $class();
            $tableName = $instance->getTableName();
            $instance->dropTableIfExists($tableName);
        }
    }
}
