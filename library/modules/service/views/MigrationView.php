<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\service\views;

use usni\library\views\UiView;
use usni\UsniAdaptor;
/**
 * MigrationView class file.
 * @package usni\library\modules\service\views
 */
class MigrationView extends UiView
{
    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        //TODO to be fixed
        $commandPath    = UsniAdaptor::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
        $runner         = new CConsoleCommandRunner();
        $runner->addCommands($commandPath);
        $commandPath    = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
        $runner->addCommands($commandPath);
        $args = array('yiic', 'migrate', '--interactive=0');
        ob_start();
        $runner->run($args);
        $content = htmlentities(ob_get_clean(), null, UsniAdaptor::app()->charset);
        return UsniAdaptor::app()->controller->renderPartial('usni.themes.bootstrap.views.site._general', array('content' => $content,
                                                                              'title'   => getLabel('application', 'output'),
                                                                              'footer'  => ''),
                                                                              true, false);
    }
}
?>