<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\service\views;

use usni\library\views\UiView;
use usni\UsniAdaptor;
/**
 * FunctionalTestView class file.
 * @package usni\library\modules\service\views
 */
class FunctionalTestView extends UiView
{
    /**
     * Renders content.
     * @return string
     */
    protected function renderContent()
    {
        //TODO - To be fixed later
        $testPath = UsniAdaptor::getAlias('application.tests');
        $cwd      = getcwd();
        chdir($testPath);
        $output   = array();
        exec('phpunit -c phpunit-functional.xml functional/cms/PostControllerTest', $output);
        chdir($cwd);
        foreach($output as $index => $v)
        {
            $output[$index] = ($v == '' ? '<br/>': $v);
        }
        $content = implode('', $output);
        return UsniAdaptor::app()->controller->renderPartial('//site/_general', array('content' => $content,
                                                                              'title'   => getLabel('application', 'output'),
                                                                              'footer'  => ''),
                                                                              true, false);
    }
}
?>