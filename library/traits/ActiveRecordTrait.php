<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\traits;

use usni\library\components\UiBaseActiveRecord;
use usni\UsniAdaptor;
use usni\library\modules\users\models\User;
/**
 * Helper methods used for the active records in the application.
 * 
 * @package usni\library\traits
 */
trait ActiveRecordTrait
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->on(UiBaseActiveRecord::EVENT_BEFORE_INSERT, [$this, 'updateCreatedDateTimeFields']);
        $this->on(UiBaseActiveRecord::EVENT_BEFORE_INSERT, [$this, 'updateModifiedDateTimeFields']);
        $this->on(UiBaseActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'updateModifiedDateTimeFields']);
    }
    
    /**
     * Add created fields to the table if not exists.
     * @param \yii\base\ModelEvent $event
     */
    public function addCreatedFields($event)
    {
        if($this->shouldAddCreatedAndModifiedFields())
        {
            $this->addNonExistingColumn($this->tableName(), 'created_by', 'integer not null');
            $this->addNonExistingColumn($this->tableName(), 'created_datetime', 'datetime not null');
            //refresh the schema so that new addition of columns are taking care of
            UsniAdaptor::db()->getSchema()->refresh();
        }
    }
    
    /**
     * Add modified fields to the table if not exists.
     * @param \yii\base\ModelEvent $event
     */
    public function addModifiedFields($event)
    {
        if($this->shouldAddCreatedAndModifiedFields())
        {
            $this->addNonExistingColumn($this->tableName(), 'modified_by', 'integer not null');
            $this->addNonExistingColumn($this->tableName(), 'modified_datetime', 'datetime not null');
            //refresh the schema so that new addition of columns are taking care of
            UsniAdaptor::db()->getSchema()->refresh();
        }
    }

    /**
     * Create column if not existing.
     * @param string $table    Table Name.
     * @param string $column   Column Name.
     * @param string $metadata Column Metadata.
     * @return void
     */
    public function addNonExistingColumn($table, $column, $metadata)
    {
        $columnMetadata = UsniAdaptor::db()->getSchema()->getTableSchema($table)->getColumn($column);
        if ($columnMetadata == null)
        {
            UsniAdaptor::db()->createCommand()->addColumn($table, $column, $metadata)->execute();
            //refresh the schema so that new addition of columns are taking care of
            UsniAdaptor::db()->getSchema()->refresh();
        }
    }
    
    /**
     * Update user and datetime field by attribute
     * @param string $scenario
     */
    public function updateDateTimeFieldByAttribute($scenario)
    {
        $isInstalled = UsniAdaptor::app()->isInstalled();
        if($scenario == 'create')
        {
            $userField = 'created_by';
            $timeField = 'created_datetime';
        }
        else
        {
            $userField = 'modified_by';
            $timeField = 'modified_datetime';
        }
        $userModel  = UsniAdaptor::app()->user->getUserModel();
        if(!$isInstalled)
        {
           $this->$userField = User::SUPER_USER_ID;
        }
        else
        {
            $userModel  = UsniAdaptor::app()->user->getUserModel();
            if($userModel != null)
            {
                $this->$userField = $userModel['id'];
            }
            else
            {
                $this->$userField = 0;
            }
        }
        $this->$timeField = date('Y-m-d H:i:s');
    }

    /**
     * Updates active record date time fields if exists.
     * @param \yii\db\ModelEvent $event
     */
    public function updateModifiedDateTimeFields($event)
    {
        if($this->shouldAddCreatedAndModifiedFields())
        {
            $this->addModifiedFields($event);
            $this->updateDateTimeFieldByAttribute('update');
        }
    }
    
    /**
     * Updates active record date time fields if exists.
     * @param \yii\db\ModelEvent $event
     */
    public function updateCreatedDateTimeFields($event)
    {
        if($this->shouldAddCreatedAndModifiedFields())
        {
            $this->addCreatedFields($event);
            $this->updateDateTimeFieldByAttribute('create');
        }
    }
}