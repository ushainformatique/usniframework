<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use yii\bootstrap\Modal;
/**
 * UiModalView class file.
 * @package usni\library\views
 */
class UiModalView extends UiView
{
    /**
     * @var string
     */
    protected $modalId;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $view;
    
    /**
     * Class constructor
     * @param string $modalId
     * @param string $title
     * @param View $view
     */
    public function __construct($modalId, $title, $view = null)
    {
        $this->modalId = $modalId;
        $this->title   = $title;
        $this->view    = $view;
    }

    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $body = $this->renderBody();
        $options = ['id' => $this->modalId];
        $options = \usni\library\utils\ArrayUtil::merge($options, ['size' => Modal::SIZE_LARGE]);
        ob_start();
        Modal::begin($options);
        echo $body;
        Modal::end();
        return ob_get_clean();
    }
    
    /**
     * @inheritdoc
     */
    protected function renderBody()
    {
        return null;
    }
    
    /**
     * @inheritdoc
     */
    protected function renderFooter()
    {
        return null;
    }
}
?>