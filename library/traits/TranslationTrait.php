<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\traits;

use usni\library\components\LanguageManager;
use yii\db\ActiveRecord;
use usni\UsniAdaptor;
use usni\library\utils\CacheUtil;
/**
 * The trait class is inspired by https://github.com/2amigos/yii2-translateable-behavior
 * and its code is being used here and changes have been done here to meet the 
 * requirements.
 * @package usni\library\components
 */
trait TranslationTrait
{
    /**
     * @var string the name of the translations relation
     */
    public $relation = 'translations';
    /**
     * @var string the locale field used in the related table. Determines the language to query | save.
     */
    public $localeField = 'language';
    /**
     * @var array the list of attributes to translate.
     */
    public $translationAttributes = [];
    /**
     * @var ActiveRecord[] the models holding the translations.
     */
    protected $_models = [];
    /**
     * @var string the language selected.
     */
    private $_language;
    /**
     * Available languages in which translations has to be done.
     * @var array
     */
    private $_languages;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->on(ActiveRecord::EVENT_AFTER_FIND, [$this, 'onFindRecord']);
        $this->on(ActiveRecord::EVENT_AFTER_INSERT, [$this, 'afterInsert']);
        $this->on(ActiveRecord::EVENT_AFTER_UPDATE, [$this, 'afterUpdate']);
        $this->translationAttributes = static::getTranslatableAttributes();
    }
    
    /**
     * Make [[$translationAttributes]] writable
     */
    public function __set($name, $value)
    {
        if (in_array($name, $this->translationAttributes))
        {
            $this->getTranslation()->$name = $value;
        }
        else
        {
            parent::__set($name, $value);
        }
    }

    /**
     * Make [[$translationAttributes]] readable
     * @inheritdoc
     */
    public function __get($name)
    {
        if (!in_array($name, $this->translationAttributes) && !isset($this->_models[$name]))
        {
            return parent::__get($name);
        }
        if (isset($this->_models[$name]))
        {
            return $this->_models[$name];
        }
        $model = $this->getTranslation();
        return $model->$name;
    }

    /**
     * Expose [[$translationAttributes]] writable
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        return in_array($name, $this->translationAttributes) ? true : parent::canSetProperty($name, $checkVars, $checkBehaviors);
    }

    /**
     * Expose [[$translationAttributes]] readable
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        return in_array($name, $this->translationAttributes) ? true : parent::canGetProperty($name, $checkVars, $checkBehaviors);
    }

    /**
     * @param \yii\base\Event $event
     */
    public function onFindRecord($event)
    {
        $this->populateTranslations();
        $this->getTranslation($this->getLanguage());
    }

    /**
     * @param \yii\base\Event $event
     */
    public function afterInsert($event)
    {
        $this->saveTranslation();
    }

    /**
     * @param \yii\base\Event $event
     */
    public function afterUpdate($event)
    {
        $this->saveTranslation();
    }

    /**
     * Sets current model's language
     *
     * @param $value
     */
    public function setLanguage($value)
    {
        if (!isset($this->_models[$value]))
        {
            $this->_models[$value] = $this->loadTranslation($value);
        }
        $this->_language = $value;
    }

    /**
     * Returns current models' language. If null, will return app's configured language.
     * @return string
     */
    public function getLanguage()
    {
        if ($this->_language === null)
        {
            $language = UsniAdaptor::app()->languageManager->getContentLanguage();
            if($language == null)
            {
                $language = UsniAdaptor::app()->language;
            }
            $this->_language = $language;
        }
        return $this->_language;
    }

    /**
     * Saves current translation model
     * @return bool
     */
    public function saveTranslation()
    {
        $model = $this->getTranslation();
        $dirty = $model->getDirtyAttributes();
        if (empty($dirty)) 
        {
            return true; // we do not need to save anything
        }
        $model->owner_id = $this->getPrimaryKey();
        return $model->save();
    }

    /**
     * Returns a related translation model
     *
     * @param string|null $localeFieldValue the language to return. If null, current sys language
     *
     * @return ActiveRecord
     */
    public function getTranslation($localeFieldValue = null)
    {
        if ($localeFieldValue === null)
        {
            $localeFieldValue = $this->getLocaleFieldValue();
        }

        if (!isset($this->_models[$localeFieldValue]))
        {
            $this->_models[$localeFieldValue] = $this->loadTranslation($localeFieldValue);
        }

        return $this->_models[$localeFieldValue];
    }

    /**
     * Loads all specified locale fields. For example:
     *
     * ```
     * $model->loadTranslations("en-US");
     *
     * $model->loadTranslations(["en-US", "es-ES"]);
     *
     * ```
     *
     * @param string|array $localeFieldValues
     */
    public function loadTranslations($localeFieldValues)
    {
        $localeFieldValues = (array) $localeFieldValues;

        foreach ($localeFieldValues as $localeFieldValue)
        {
            $this->loadTranslation($localeFieldValue);
        }
    }

    /**
     * Loads a specific translation model
     *
     * @param string $localeFieldValue the locale field value to return
     *
     * @return null|\yii\db\ActiveQuery|static
     */
    public function loadTranslation($localeFieldValue)
    {
        $translation = null;
        $cacheKey    = null;
        /** @var \yii\db\ActiveQuery $relation */
        $relation = $this->getRelation($this->relation);
        /** @var ActiveRecord $class */
        $class = $relation->modelClass;
        if ($this->getPrimarykey())
        {
            $cacheKey    = $this->getCacheKey($class, $localeFieldValue, $this->getPrimarykey());
            $translation = CacheUtil::get($cacheKey);
            if($translation === false)
            {
                $translation = $class::findOne(
                                [$this->localeField => $localeFieldValue, key($relation->link) => $this->getPrimarykey()]
                );
            }
        }
        if ($translation === null)
        {
            $translation = new $class;
            $translation->{key($relation->link)} = $this->getPrimaryKey();
            $translation->{$this->localeField} = $localeFieldValue;
            CacheUtil::set($cacheKey, $translation);
            CacheUtil::setModelCache($class, $cacheKey);
        }
        return $translation;
    }
    
    /**
     * Get languages.
     * @return array
     */
    public function getLanguages()
    {
        if(empty($this->_languages))
        {
            $this->_languages = LanguageManager::getCodeList();
        }
        return $this->_languages;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        $translatedClassName = $this->resolveTranslatedModelClassName();
        if($translatedClassName == null)
        {
            $className              = get_class($this);
            // Remove 'Search' from class name, eg.: NewsSearch -> News
            if (substr($className, -6, 6) == 'Search') 
            {
                $className = str_replace('Search', '', $className);
            }
            $translatedClassName    = $className . 'Translated';
        }
        return $this->hasMany($translatedClassName, ['owner_id' => 'id']);
    }
    
    /**
     * Populates already loaded translations
     */
    private function populateTranslations()
    {
        //translations
        $aRelated = $this->getRelatedRecords();
        if (isset($aRelated[$this->relation]) && $aRelated[$this->relation] != null) 
        {
            if (is_array($aRelated[$this->relation])) 
            {
                foreach ($aRelated[$this->relation] as $model) 
                {
                    $this->_models[$model->getAttribute($this->localeField)] = $model;
                }
            } 
            else 
            {
                $model = $aRelated[$this->relation];
                $this->_models[$model->getAttribute($this->localeField)] = $model;
            }
        }
    }
    
    /**
     * Get locale field value
     * @return mixed
     */
    protected function getLocaleFieldValue()
    {
        return $this->getLanguage();
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete())
        {
            /** @var \yii\db\ActiveQuery $relation */
            $relation   = $this->getRelation($this->relation);
            /** @var ActiveRecord $class */
            $class      = $relation->modelClass;
            $class::deleteAll('owner_id = :id AND language = :language', [':id' => $this->id, ':language' => UsniAdaptor::app()->language]);
            $languages  = $this->getLanguages();
            foreach($languages as $language)
            {
                if($language != UsniAdaptor::app()->language)
                {
                    $class::deleteAll('owner_id = :id AND language = :language', [':id' => $this->id, ':language' => $language]);
                }
            }
            return true;
        }
        return false;
    }
    
    /**
     * Loads a specific translation model by attribute
     *
     * @param string $localeFieldValue the locale field value to return
     * @param string $attribute
     * @param mixed $value
     * @return null|\yii\db\ActiveQuery|static
     */
    public function getTranslationByAttribute($localeFieldValue = null, $attribute, $value)
    {
        if ($localeFieldValue === null)
        {
            $localeFieldValue = $this->getLocaleFieldValue();
        }
        /** @var \yii\db\ActiveQuery $relation */
        $relation = $this->getRelation($this->relation);
        /** @var ActiveRecord $class */
        $class = $relation->modelClass;
        
        $translation = $class::findOne(
                        [$this->localeField => $localeFieldValue, $attribute => $value]
        );
        return $translation;
    }
    
    /**
     * Get cache key
     * @param string $class
     * @param string $language
     * @param int $id
     * @return string
     */
    protected function getCacheKey($class, $language, $id)
    {
        $className = UsniAdaptor::getObjectClassName($class);
        return $className . '_' . $language . '_' . $id;
    }
    
    /**
     * Resolve translated model class name
     * @return string
     */
    protected function resolveTranslatedModelClassName()
    {
        return null;
    }
}