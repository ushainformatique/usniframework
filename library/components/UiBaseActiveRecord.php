<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\UsniAdaptor;
use usni\library\utils\ArrayUtil;
use usni\library\exceptions\MethodNotImplementedException;
use usni\library\utils\ObjectUtil;
use usni\library\exceptions\FailedToSaveModelException;
use usni\library\utils\CacheUtil;
use yii\helpers\Json;
/**
 * Base active record class for the application.
 * 
 * @package usni\library\components
 */
class UiBaseActiveRecord extends \yii\db\ActiveRecord
{
    use \usni\library\traits\ActiveRecordTrait;
    /**
     * Active status constant.
     */
    const STATUS_ACTIVE = 1;
    /**
     * Inactive status constant.
     */
    const STATUS_INACTIVE = 0;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $className = UsniAdaptor::getObjectClassName(get_called_class());
        if(strpos($className, 'Search') !== false)
        {
            $namespace          = ObjectUtil::getClassNamespace(get_called_class());
            $parentClassName    = substr($className, 0, -6);
            $fullyQualifiedParentClassName = $namespace . '\\' . $parentClassName;
            return $fullyQualifiedParentClassName::tableName();
        }
        return parent::tableName();
    }

    /**
     * Get translated attribute labels.
     * @param string $labels Attribute labels.
     * @return array
     */
    public static function getTranslatedAttributeLabels($labels)
    {
        return ArrayUtil::merge($labels, array( 'created_by'        => UsniAdaptor::t('application','Created By'),
                                                'created_datetime'  => UsniAdaptor::t('application','Created Date Time'),
                                                'modified_by'       => UsniAdaptor::t('application','Modified By'),
                                                'modified_datetime' => UsniAdaptor::t('application','Modified Date Time')));
    }

    /**
     * Get singular or plural label.
     * @return string
     * @throws exception MethodNotImplementedException.
     */
    public static function getLabel($n = 1)
    {
        throw new MethodNotImplementedException('getLabel', get_called_class());
    }

    /**
     * Find record by attribute. This would not work with translated atributes
     * @param string $attribute Attribute name.
     * @param string $value     Attribute value.
     * @return ActiveRecord
     */
    public static function findByAttribute($attribute, $value)
    {
        $modelClass     = get_called_class();
        $condition      = $attribute . "= '" . $value . "'";
        return $modelClass::find()->where($condition)->one();
    }

    /**
     * Get required attributes for the model.
     * @return array
     */
    public function getRequiredAttributes()
    {
        $requiredAttributes = array();
        $attributes         = ArrayUtil::merge($this->attributes(), ObjectUtil::getClassPublicProperties(get_called_class(), true));
        foreach($attributes as $attribute)
        {
            if($this->isAttributeRequired($attribute))
            {
                $requiredAttributes[] = $attribute;
            }
        }
        return $requiredAttributes;
    }

    /**
     * Find the record and if not exist insert the data.
     *
     * @param string $attribute Attribute name.
     * @param string $value     Attribute value.
     * @param string $data      ActiveRecord.
     * @param string $scenario  Scenario of the model.
     * @return void
     * @throws exception FailedToSaveModelException.
     */
    public static function findByAttributeAndInsert($attribute, $value, $data, $scenario = 'create')
    {
        assert('is_string($attribute)');
        assert('is_array($data)');
        if(static::findByAttribute($attribute, $value) == null)
        {
            $modelClass = get_called_class();
            $model      = new $modelClass(['scenario' => $scenario]);
            $model->setAttributes($data, true);
            if(!$model->save())
            {
                throw new FailedToSaveModelException($modelClass);
            }
        }
    }

    /**
     * Should created and modified fields be added to the model.
     * @return boolean
     */
    public function shouldAddCreatedAndModifiedFields()
    {
        return true;
    }

    /**
     * Get record by name.
     * @param string $name
     * @param string $language
     * @return Model
     */
    public static function findByName($name, $language = null)
    {
        $class          = get_called_class();
        $activeQuery    = $class::find();
        if(is_subclass_of($class, TranslatableActiveRecord::className()))
        {
            if($language == null)
            {
                $language = UsniAdaptor::app()->language;
            }
            if(in_array('name', static::getTranslatableAttributes()))
            {
                $activeQuery = $activeQuery->joinWith('translations', true, 'INNER JOIN');
                return $activeQuery->where('name = :name AND language = :language', [':name' => $name, ':language' => $language])->one();
            }
            else
            {
                return $activeQuery->where('name = :name', [':name' => $name])->one();
            }
        }
        else
        {
            return $activeQuery->where('name = :name', [':name' => $name])->one();
        }
        return null;
    }
    
    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $modelCacheKey  = UsniAdaptor::getObjectClassName(get_class($this)) . 'Cache';
        $this->deleteModelCache($modelCacheKey);
    }
    
    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        $modelCacheKey  = UsniAdaptor::getObjectClassName(get_class($this)) . 'Cache';
        $this->deleteModelCache($modelCacheKey);
    }
    
    /**
     * Delete model cache
     * @param string $modelCacheKey
     * @return void
     */
    public function deleteModelCache($modelCacheKey)
    {
        $value          = CacheUtil::get($modelCacheKey);
        if($value !== false)
        {
            $data = Json::decode($value);
            foreach($data as $key)
            {
                CacheUtil::delete($key);
            }
        }
    }
    
    /**
     * Get model configuration
     * @return array
     */
    public function getModelConfig()
    {
        $modelConfigFilePath = UsniAdaptor::getAlias('@common/config/modelConfig.php');
        $config    = [];
        $shortName = strtolower(UsniAdaptor::getObjectClassName($this));
        $namespace = ObjectUtil::getClassNamespace(get_class($this));
        $alias     = str_replace('\\', '/', $namespace);
        $path      = UsniAdaptor::getAlias($alias);
        if(file_exists($path . '/config/' . $shortName . '.php'))
        {
            $config = require($path . '/config/' . $shortName . '.php');
        }
        elseif (file_exists($modelConfigFilePath))
        {
            $config = require($modelConfigFilePath);
            $className  = get_class($this);
            $classConfig = ArrayUtil::getValue($config, $className, []);
            return $classConfig;
        }
        return $config;
    }
    
    /**
     * Check if extended config exists for the model
     * @return boolean
     */
    public function checkIfExtendedConfigExists()
    {
        $extendedModelConfig = UsniAdaptor::app()->extendedModelsConfig;
        $calledClass         = get_class($this);
        if(is_array($extendedModelConfig) && ArrayUtil::getValue($extendedModelConfig, $calledClass) != null)
        {
            return true;
        }
        return false;
    }
    
    /**
     * Get extended config class instance
     * @return \usni\library\components\modelConfigClass
     */
    public function getExtendedConfigClassInstance()
    {
        $calledClass            = get_class($this);
        $extendedModelConfig    = UsniAdaptor::app()->extendedModelsConfig;
        $modelConfigClass       = $extendedModelConfig[$calledClass];
        $configClassInstance    = new $modelConfigClass($this);
        return $configClassInstance;
    }
}