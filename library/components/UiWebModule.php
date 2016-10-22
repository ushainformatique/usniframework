<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\library\utils\ObjectUtil;
use Yii;
/**
 * This is the base module class for the framework.
 * @package usni\library\components
 */
abstract class UiWebModule extends \yii\base\Module
{
    /**
     * Extended controller path to search for extended controllers. This should be provided
     * as alias.
     * @var array
     */
    public $extendedControllerPath = array();
    /**
     * Extended controller map to search for extended controllers.
     * @var array
     */
    public $extendedControllerMap = array();
    /**
     * Data manager path.
     * @var array
     */
    public $dataManagerPath = array();
    /**
     * Grid view mapping for the module.
     * @var array
     */
    public $gridViewMapping = [];
    /**
     * Detail view mapping for the module.
     * @var array
     */
    public $detailViewMapping = [];
    /**
     * Is core module
     * @var boolean 
     */
    public $isCoreModule;
    /**
     * status
     * @var boolean 
     */
    public $status;
    /**
     * Can be disabled
     * @var boolean 
     */
    public $canBeDisabled;
    /**
     * Table manager.
     * @var string
     */
    public $tableManager;
    /**
     * Menu manager.
     * @var string
     */
    public $menuManager;
    
    /**
     * Data manager.
     * @var string
     */
    public $dataManager;
    
    /**
     * Initializes the module.
     * @return void
     */
    public function init()
    {
        parent::init();
        //Include the config
        $configFile = $this->getBasePath() . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'main.php';
        if(file_exists($configFile))
        {
            $config = require($configFile);
            if(!empty($config))
            {
                Yii::configure($this, $config);
            }
        }
    }

    /**
     * Gets the parameter value for a module for example UsniAdaptor::app()->getModule('users')->getParam('xyz').
     * @param string $key .
     * @return null
     */
    public function getParam($key)
    {
        $params = $this->getParams();
        if(isset($params[$key]))
        {
            return $params[$key];
        }
        return null;
    }

    /**
     * Gets capitalized id.
     * @return string
     */
    public function getCapitalizedId()
    {
        return ucfirst($this->getId());
    }

    /**
     * Sets data manager path.
     * @param array $paths
     * @return void
     */
    public function setDataManagerPath($paths = array())
    {
        $this->dataManagerPath = $paths;
    }

    /**
     * Get data manager path.
     * @return array
     */
    public function getDataManagerPath()
    {
        if(!empty($this->dataManagerPath))
        {
            return $this->dataManagerPath;
        }
        $namespace       = $this->getNamespace();
        $basePath        = $namespace . '\managers';
        return array($basePath);
    }

    /**
     * Should render menu in sidebar.
     * @return boolean
     */
    public function shouldRenderMenuInSidebar()
    {
        return true;
    }
    
    /**
     * Gets module namespace.
     * @return string
     */
    public function getNamespace()
    {
        return ObjectUtil::getClassNamespace(get_class($this));
    }
    
    /**
     * Get menu manager.
     * @return string
     */
    public function getMenuManager()
    {
        if(!empty($this->menuManager))
        {
            return $this->menuManager;
        }
        $namespace       = $this->getNamespace();
        return $namespace . '\managers\MenuManager';
    }
    
    /**
     * Sets menu manager.
     * @param $className string
     * @return void
     */
    public function setMenuManager($className)
    {
        $this->menuManager = $className;
    }
    
    /**
     * Sets table manager.
     * @param $className string
     * @return void
     */
    public function setTableManager($className)
    {
        $this->tableManager = $className;
    }

    /**
     * Get table manager.
     * @return array
     */
    public function getTableManager()
    {
        if(!empty($this->tableManager))
        {
            return $this->tableManager;
        }
        $namespace       = $this->getNamespace();
        return $namespace . '\managers\TableManager';
    }
    
    /**
     * Get data manager.
     * @return string
     */
    public function getDataManager()
    {
        return $this->dataManager;
    }
    
    /**
     * Sets data manager.
     * @param $className string
     * @return void
     */
    public function setDataManager($className)
    {
        $this->dataManager = $className;
    }
}
?>