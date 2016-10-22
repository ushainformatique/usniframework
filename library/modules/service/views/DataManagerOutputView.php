<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\service\views;

use usni\library\views\UiView;
use usni\UsniAdaptor;
/**
 * DataManagerOutputView class file.
 * @package usni\library\modules\service\views
 */
class DataManagerOutputView extends UiView
{
    /**
     * Prefix for data manager class.
     * @var string 
     */
    protected $prefix;
    
    /**
     * Class constructor.
     * @param string $prefix
     */
    public function __construct($prefix)
    {
        $this->prefix  = $prefix;
    }
    
    /**
     * Renders content.
     * @return string
     */
    protected function renderContent()
    {
        $className = $this->prefix . 'DataManager';
        $className::loadDefaultData();
        return UsniAdaptor::app()->controller->renderPartial('//site/_general', 
                                             ['content' => UsniAdaptor::t('service', 'Data load for {class} is successfull', ['{class}' => $this->prefix]),
                                              'title'   => UsniAdaptor::t('application', 'Output'),
                                              'footer'  => '']);
    }
}
?>