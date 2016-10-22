<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\UsniAdaptor;
use usni\library\components\UiHtml;
use yii\widgets\Breadcrumbs;
/**
 * UiBreadCrumbView class file.
 * @package usni\library\views
 */
class UiBreadCrumbView extends UiView
{
    /**
     * @return string
     */
    protected function renderContent()
    {
        $content = Breadcrumbs::widget(
                                        [
                                            'links'                => $this->getView()->params['breadcrumbs'],
                                            'homeLink'             => $this->getHomeLink(),
                                        ]);
        //return UiHtml::tag('div', $content, ['class' => 'breadcrumb-row']);
        return UiHtml::tag('div', $content, ['class' => 'breadcrumb-line', 'style' => 'margin-top:20px;']);
    }

    /**
     * Gets home link.
     * @return string
     */
    protected function getHomeLink()
    {
        return ['label' => UsniAdaptor::t('application', 'Dashboard'),
                'url'   => UsniAdaptor::createUrl('home/default/dashboard')];
    }
}
?>
