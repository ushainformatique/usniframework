<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\UsniAdaptor;
use usni\library\utils\FileUtil;
/**
 * ObjectUtil class file.
 * 
 * @package usni\library\utils
 */
class ObjectUtil
{
    /**
     * Get class public properties.
     * @param string $className
     * @param boolean $ignoreParentProperties Ignore parent class properties.
     * @return array
     */
    public static function getClassPublicProperties($className, $ignoreParentProperties = true)
    {
        $reflectionClass         = new \ReflectionClass($className);
        $publicPropertiesObjects = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);
        $publicProperties        = array();
        foreach($publicPropertiesObjects as $object)
        {
            if($ignoreParentProperties)
            {
                if($object->class == $className)
                {
                    $publicProperties[] = $object->name;
                }
            }
            else
            {
                $publicProperties[] = $object->name;
            }
        }
        return $publicProperties;
    }
    
    /**
     * Create translated model.
     * @param string $baseModelClassName
     * @return string|void
     */
    public static function createTranslatedModel($baseModelClassName)
    {
        if(strpos($baseModelClassName, 'Search') !== false)
        {
            return;
        }
        $translatedModelClassName = $baseModelClassName . 'Translated';
        $shortClassName           = UsniAdaptor::getObjectClassName($translatedModelClassName);
        $namespace                = self::getClassNamespace($baseModelClassName);
        $content                  = UsniAdaptor::app()->getView()->renderFile('@usni/library/generators/views/_translatedModelTemplate.php', 
                                                                                ['namespace'                => $namespace,
                                                                                 'translatedModelClassName' => $shortClassName,
                                                                                 'modelClassName'           => $baseModelClassName]);
        $path                     = self::getPathByAlias($namespace);
        $fileName                 = $shortClassName . '.php';
        if(!file_exists($path . '/' . $fileName))
        {
            FileUtil::writeFile($path, $fileName, 'wb', $content);
        }
    }
    
    /**
     * Get class namespace.
     * @param string $fullyQualifiedClassName
     * @return string
     */
    public static function getClassNamespace($fullyQualifiedClassName)
    {
        $reflectionClass = new \ReflectionClass($fullyQualifiedClassName);
        return $reflectionClass->getNamespaceName();
    }
    
    /**
     * Get full path by alias.
     * @param string $aliasedPath
     * @return string
     */
    public static function getPathByAlias($aliasedPath)
    {
        $path = UsniAdaptor::getAlias('@' . str_replace('\\', '/', $aliasedPath));
        return str_replace('\\', '/', $path);
    }
}