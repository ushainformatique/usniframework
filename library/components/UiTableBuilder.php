<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use yii\base\Component;
use yii\di\Instance;
use yii\db\Connection;
use yii\db\Schema;
use usni\UsniAdaptor;
use usni\library\utils\ArrayUtil;
/**
 * UiTableBuilder is the abstract class for table creation in the application.
 * @package usni\library\components
 */
abstract class UiTableBuilder extends Component
{
    use \yii\db\SchemaBuilderTrait;
    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection
     * that this migration should work with. Starting from version 2.0.2, this can also be a configuration array
     * for creating the object.
     *
     * Note that when a Migration object is created by the `migrate` command, this property will be overwritten
     * by the command. If you do not want to use the DB connection provided by the command, you may override
     * the [[init()]] method like the following:
     *
     * ```php
     * public function init()
     * {
     *     $this->db = 'db2';
     *     parent::init();
     * }
     * ```
     */
    public $db = 'db';
    
    /**
     * Name of the table
     * @var string 
     */
    public $tableName;
    
    /**
     * Options for the table.
     * @var array 
     */
    public $tableOptions = [];
    
    /**
     * Initializes the class.
     * This method will set [[db]] to be the 'db' application component, if it is null.
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
    }
    
    /**
     * Build table
     * @return void
     */
    public function buildTable()
    {
        $this->db->schema->refresh();
        $this->tableName    = $this->getTableName();
        $tableSchema        = $this->db->schema->getTableSchema($this->tableName);
        if($tableSchema != null)
        {
            return;
        }
        $this->tableOptions = $this->getDefaultTableOptions();
        $data               = $this->getTableSchema();
        if($this->doesCreateUpdateFieldsRequired())
        {
            $data = ArrayUtil::merge($data, static::getCreatedAndModifiedFields());
        }
        try
        {
            $this->getCommand()->createTable($this->tableName, $data, $this->tableOptions)->execute();
            $this->buildIndex();
            if(static::isTranslatable())
            {
                $currentNs               = $this->getNamespace();
                $trTableBuilderClassName = $currentNs . '\\' . $this->getTranslatedTableBuilderClassName();
                $trTableBuilder = new $trTableBuilderClassName();
                $trTableBuilder->buildTable();
            }
        }
        catch (\Exception $e)
        {
            \Yii::error('Building table fails for ' . $this->tableName . ' with error '. $e->getMessage() , __METHOD__);
            throw $e;
        }
    }
    
    /**
     * Gets table schema.
     * @return array
     */
    abstract protected function getTableSchema();

    /**
     * Does create update fields required.
     * @return boolean
     */
    protected function doesCreateUpdateFieldsRequired()
    {
        return true;
    }

    /**
     * Get command object
     * @param string $sql
     * @param array $params
     * @return Command
     */
    public function getCommand($sql = null, $params = [])
    {
        return $this->db->createCommand($sql, $params);
    }
    
    /**
     * Gets default table options.
     * @return string
     */
    public function getDefaultTableOptions()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        return $tableOptions;
    }
    
    /**
     * Get created and modified fields.
     * @return array
     */
    public static function getCreatedAndModifiedFields()
    {
        return [
                'created_by'    => Schema::TYPE_INTEGER . '(11) NOT NULL',
                'modified_by'   => Schema::TYPE_INTEGER . '(11) NOT NULL',
                'created_datetime' => Schema::TYPE_DATETIME . ' NOT NULL',
                'modified_datetime' => Schema::TYPE_DATETIME . ' NOT NULL'
               ];
    }
    
    /**
     * Does table exist in database.
     * @param string $table
     * @return TableSchema table metadata. Null if the named table does not exist.
     */
    public function doesTableExist($table)
    {
        return $this->db->schema->getTableSchema($table);
    }
    
    /**
     * Drop table if exists.
     * @param string $table
     * @return void
     */
    public function dropTableIfExists($table)
    {
        if($this->doesTableExist($table) != null)
        {
            $this->getCommand()->dropTable($table)->execute();
            if(static::isTranslatable())
            {
                $currentNs               = $this->getNamespace();
                $trTableBuilderClassName = $currentNs . '\\' . $this->getTranslatedTableBuilderClassName();
                $trTableBuilder          = new $trTableBuilderClassName();
                $trTableName             = $trTableBuilder->getTableName();
                $trTableBuilder->dropTableIfExists($trTableName);
            }
        }
    }
    
    /**
     * Build indexes for the table.
     * @return void
     */
    public function buildIndex()
    {
        $indexes = $this->getIndexes();
        if(!empty($indexes))
        {
            foreach($this->getIndexes() as $indexData)
            {
                $this->getCommand()->createIndex($indexData[0], $this->tableName, $indexData[1], $indexData[2])->execute();
            }
        }
    }
    
    /**
     * Get table name.
     * @return string
     */
    public function getTableName()
    {
        $className          = UsniAdaptor::getObjectClassName($this);
        $qualifiedClassName = get_class($this);
        $managerPathLength  = 2 + strlen('managers') + strlen($className);
        $basePath           = substr($qualifiedClassName, 0, -$managerPathLength);
        $modelClassName     = substr($className, 0, -12);
        $qualifiedModelName = $basePath . '\\models\\' . $modelClassName;
        return $qualifiedModelName::tableName();
    }
    
    /**
     * Get indexes for the table.
     * @return array
     */
    protected function getIndexes()
    {
        return [];
    }
    
    /**
     * Checks if translatable table exists or not
     * @return boolean
     */
    protected static function isTranslatable()
    {
        return false;
    }
    
    /**
     * Gets translated table builder class name.
     * @return string
     * @throws \usni\library\exceptions\MethodNotImplementedException
     */
    protected function getTranslatedTableBuilderClassName()
    {
        $builderClassName = UsniAdaptor::getObjectClassName($this);
        $modelClassName   = substr($builderClassName, 0, -12);
        $trBuilderClassName = $modelClassName . 'TranslatedTableBuilder';
        return $trBuilderClassName;
    }
    
    /**
     * Gets module namespace.
     * @return string
     */
    public function getNamespace()
    {
        $reflectionClass = new \ReflectionClass(get_class($this));
        return $reflectionClass->getNamespaceName();
    }
    
    /**
     * @inheritdoc
     */
    protected function getDb()
    {
        return $this->db;
    }
    
    /**
     * Get table sql
     * @return string
     * @throws \Exception
     */
    public function getTableSql()
    {
        $this->tableName    = $this->getTableName();
        $tableSchema        = $this->db->schema->getTableSchema($this->tableName);
        if($tableSchema != null)
        {
            return;
        }
        $this->tableOptions = $this->getDefaultTableOptions();
        $data               = $this->getTableSchema();
        if($this->doesCreateUpdateFieldsRequired())
        {
            $data = ArrayUtil::merge($data, static::getCreatedAndModifiedFields());
        }
        try
        {
            $cmd = $this->getCommand()->createTable($this->tableName, $data, $this->tableOptions);
            return $cmd->getSql();
        }
        catch (\Exception $e)
        {
            \Yii::error('Create table query fails for ' . $this->tableName . ' with error '. $e->getMessage() , __METHOD__);
            throw $e;
        }
    }
    
    /**
     * Get index sql
     * @return string
     * @throws \Exception
     */
    public function getIndexSql()
    {
        $indexSqls          = [];
        $this->tableName    = $this->getTableName();
        $tableSchema        = $this->db->schema->getTableSchema($this->tableName);
        if($tableSchema != null)
        {
            return;
        }
        try
        {
            $indexes = $this->getIndexes();
            if(!empty($indexes))
            {
                foreach($this->getIndexes() as $indexData)
                {
                    $cmd         = $this->getCommand()->createIndex($indexData[0], $this->tableName, $indexData[1], $indexData[2]);
                    $indexSqls[] = $cmd->getSql();
                }
            }
        }
        catch (\Exception $e)
        {
            \Yii::error('Create index query fails for ' . $this->tableName . ' with error '. $e->getMessage() , __METHOD__);
            throw $e;
        }
        if(!empty($indexSqls))
        {
            return implode(';', $indexSqls);
        }
        return null;
    }
    
    /**
     * Get translated table sql
     * @return string
     * @throws \Exception
     */
    public function getTranslatedTableSql()
    {
        $trSql = null;
        try
        {
            if(static::isTranslatable())
            {
                $currentNs               = $this->getNamespace();
                $trTableBuilderClassName = $currentNs . '\\' . $this->getTranslatedTableBuilderClassName();
                $trTableBuilder = new $trTableBuilderClassName();
                $trSql = $trTableBuilder->getTableSql();
            }
        }
        catch (\Exception $e)
        {
            \Yii::error('Create translated table query fails for ' . $this->getTableName() . ' with error '. $e->getMessage() , __METHOD__);
            throw $e;
        }
        return $trSql;
    }
}
?>