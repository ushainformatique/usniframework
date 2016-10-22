<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\UsniAdaptor;
use usni\library\utils\ArrayUtil;
use yii\helpers\Url;
/**
 * Base Controller class file. All controller classes for this application should extend from this base class.
 * @package usni\library\components
 */
class UiBaseController extends Controller
{
    /**
     * Sets the layout to false as we are not using it.
     * @var string
     */
    public $layout = false;
    /**
     * Column view for the controller related view.
     * @var UiColumnView
     */
    private $_columnView;

    /**
     * Loads model.
     * @param string  $modelClass Model class name.
     * @param integer $id         ID of the model to be loaded.
     * @return Model
     * @throws exception HttpException.
     */
    protected function loadModel($modelClass, $id)
    {
        $id         = intval($id);
        $model      = $modelClass::findOne($id);
        if ($model === null)
        {
            throw new \yii\base\InvalidParamException();
        }
        return $model;
    }

    /**
     * Page titles.
     * @return array
     */
    public function pageTitles()
    {
        return array();
    }

    /**
     * Get page title.
     * @return string
     */
    public function getPageTitle()
    {
        $action     = $this->action->id;
        $titles     = $this->pageTitles();
        $pageTitle  = ArrayUtil::getValue($titles, $action);
        if ($pageTitle != null)
        {
            return $pageTitle;
        }
        else
        {
            return UsniAdaptor::app()->name;
        }
    }

    /**
     * Overrides to render the pagetitle based on titles defined in the controller against
     * the actions.
     * @param string $view the view name.
     * @param array $params the parameters (name-value pairs) that should be made available in the view.
     * @return string the rendering result. Null if the rendering result is not required.
     * @throws InvalidParamException if the view file or the layout file does not exist.
     */
    public function render($view, $params = [])
    {
        $title = ArrayUtil::getValue($params, 'title');
        if($title != null)
        {
            $this->getView()->title = $title;
        }
        elseif($this->getView()->title == null)
        {
            $this->getView()->title = $this->getPageTitle();
        }
        $script = '$(".select2-container").tooltip({
    title: function() {
        return $(this).next().data("hint");
    },
});';
        $this->getView()->registerJs($script, \yii\web\View::POS_READY);
        return parent::render($view, $params);
    }

    /**
     * Resolves default redirect url after login.
     * @return string
     */
    public function resolveDefaultAfterLoginUrl()
    {
        if(UsniAdaptor::app()->homeUrl == null)
        {
            $redirectUrl = Url::base(true);
        }
        else
        {
            $redirectUrl = Url::home(true);
        }
        return $redirectUrl;
    }

    /**
     * Process redirection after save.
     * @param string $route
     * @param array $params
     * @return void
     */
    protected function processRedirectionAfterSave($route, $params)
    {
        if ($route == null)
        {
            $this->redirect(UsniAdaptor::app()->request->getUrl())->send();
        }

        if (strpos($route, 'http') > 0)
        {
            $this->redirect($route)->send();
        }

        else
        {
            $url = UsniAdaptor::app()->createUrl($route, $params);
            $this->redirect($url)->send();
        }
    }

    /**
     * Renders inner content.
     * @param array $inputViews
     * @return string
     */
    public function renderColumnContent($inputViews)
    {
        return UsniAdaptor::app()->viewHelper->renderColumnContent($inputViews);
    }

    /**
     * Make list view.
     * @param string $listViewClass UiGridView.
     * @param string $modelClass    Model Class name.
     * @param string $dataProvider  DataProvider.
     * @param string $params        Params.
     * @return string
     */
    public function createListView($listViewClass, $modelClass, $dataProvider = null, $params = array())
    {
        assert('is_string($listViewClass)');
        assert('is_string($modelClass)');
        $model = $modelClass::find();
        $model->scenario = 'search';
        if (isset($_GET[$modelClass]))
        {
            $model->attributes = $_GET[$modelClass];
        }
        return new $listViewClass($model, $dataProvider, $params);
    }

    /**
     * Get column view.
     * @return string
     */
    public function getColumnView()
    {
        if($this->_columnView == null)
        {
            $this->_columnView   = UsniAdaptor::app()->viewHelper->getInstance('columnView');
        }
        return $this->_columnView;
    }

    /**
     * Sets column view.
     * @param UiColumnView $columnView
     */
    public function setColumnView($columnView)
    {
        $this->_columnView = $columnView;
    }

    /**
     * Get search form model class name.
     * @param Model $model
     * @return string
     */
    protected function getSearchFormModelClassName($model)
    {
        return $model->className();
    }
}
?>