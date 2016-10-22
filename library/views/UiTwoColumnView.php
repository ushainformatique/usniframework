<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\UsniAdaptor;
/**
 * Two Column view for the application.
 * @package usni\library\views
 */
class UiTwoColumnView extends UiColumnView
{
    const SIDENAV_LEFT  = 'left';

    const SIDENAV_RIGHT = 'right';

    /**
     * Side navigation position.
     * @var string
     */
    protected $sidenavPosition;

    /**
     * Class constructor.
     * @param array $config
     * @retrun void
     */
    public function __construct($config = array())
    {
        foreach ($config as $key => $value)
        {
            $this->$key = $value;
        }
        if($this->sidenavPosition == null)
        {
            $this->sidenavPosition = self::SIDENAV_LEFT;
        }
    }

    /**
     * Renders content.
     * @return string
     */
    protected function renderContent()
    {
        $topnav         = $this->renderTopnavbar();
        $leftnav        = $this->renderLeftNav();
        $content        = $this->renderBody();
        $rightnav       = $this->renderRightNav();
        $breadcrumbs    = $this->renderBreadcrumb();

        return $this->getView()->renderPhpFile(UsniAdaptor::getAlias($this->getLayout()) . '.php',
                                                    array('leftnav'         => $leftnav,
                                                          'rightnav'        => $rightnav,
                                                          'topnav'          => $topnav,
                                                          'content'         => $content,
                                                          'breadcrumbs'     => $breadcrumbs,
                                                          'bottombar'       => $this->renderFixedBottomBar(),
                                                          'footer'          => $this->renderFooter()
                                                         ));
    }

    /**
     * Renders left navigation.
     * @return string
     */
    protected function renderLeftNav()
    {
        $content = null;
        if($this->sidenavPosition == self::SIDENAV_LEFT)
        {
            $sidenavView = UsniAdaptor::app()->viewHelper->getInstance('sidenavView');
            $content    .= $sidenavView->render();
        }
        return $content;
    }

    /**
     * Renders right navigation.
     * @return string
     */
    protected function renderRightNav()
    {
        $content = null;
        if($this->sidenavPosition == self::SIDENAV_RIGHT)
        {
            $sidenavView = UsniAdaptor::app()->viewHelper->getInstance('sidenavView');
            $content    .= $sidenavView->render();
        }
        return $content;
    }

    /**
     * Renders fixed bottom bar.
     * @return string
     */
    protected function renderFixedBottomBar()
    {
        return null;
    }
    
    /**
     * Get layout for two column view
     * @return string
     */
    protected function getLayout()
    {
        return '@usni/themes/bootstrap/views/layouts/multicolumn';
    }
}
?>