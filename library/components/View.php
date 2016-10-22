<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

/**
 * View class file.
 * @package usni\library\components
 */
class View extends \yii\web\View
{
    /**
     * Renders a view class in response to an AJAX request.
     *
     * This method is similar to [[render()]] except that it will surround the view being rendered
     * with the calls of [[beginPage()]], [[head()]], [[beginBody()]], [[endBody()]] and [[endPage()]].
     * By doing so, the method is able to inject into the rendering result with JS/CSS scripts and files
     * that are registered with the view.
     *
     * @param Instance of class whose content has to be rendered. Please refer to [[render()]] on how to specify this parameter.
     * @return string the rendering result
     * @see render()
     */
    public function renderAjaxWithClass($view)
    {
        ob_start();
        ob_implicit_flush(false);

        $this->beginPage();
        $this->head();
        $this->beginBody();
        echo $view->render();
        $this->endBody();
        $this->endPage(true);

        return ob_get_clean();
    }
}
?>
