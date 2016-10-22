<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\extensions\bootstrap\views\UiTabbedEditView;
use usni\library\utils\FlashUtil;
use usni\library\utils\ButtonsUtil;
use usni\library\components\UiActiveForm;
use usni\library\modules\users\views\UserEditView;
use usni\library\modules\users\views\PersonEditView;
use usni\library\modules\users\views\AddressEditView;
use usni\library\modules\users\models\Person;
use usni\library\modules\users\models\Address;
use usni\UsniAdaptor;
use usni\library\utils\ArrayUtil;
/**
 * ProfileEditView class file.
 * @package usni\library\modules\users\views
 */
class ProfileEditView extends UiTabbedEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $userView       = $this->renderSubView(UserEditView::className(), $this->model->user);
        $personView     = $this->renderSubView(PersonEditView::className(), $this->model->person);
        $addressView    = $this->renderSubView(AddressEditView::className(), $this->model->address);
        $elements = [
                        'user'      => ['type' => UiActiveForm::INPUT_RAW, 'value' => $userView],
                        'person'    => ['type' => UiActiveForm::INPUT_RAW, 'value' => $personView],
                        'address'   => ['type' => UiActiveForm::INPUT_RAW, 'value' => $addressView],
        ];
        $metadata = [
                        'elements'  => $elements,
                        'buttons'   => ButtonsUtil::getDefaultButtonsMetadata('users/default/manage')
                    ];
        return $metadata;
    }

    /**
     * @inheritdoc
     */
    protected function renderFlashMessages()
    {
        return FlashUtil::render('userregistration');
    }

    /**
     * @inheritdoc
     */
    protected function resolveDefaultBrowseByAttribute()
    {
        return 'username';
    }

    /**
     * Get model dropdown list.
     * @return string
     */
    protected function renderEditModeBrowseView()
    {
        if($this->model->scenario == 'update')
        {
            $view   = new UserEditBrowseModelView(['model' =>$this->model->user, 'attribute' => $this->resolveDefaultBrowseByAttribute()]);
            return $view->render();
        }
    }
    
    /**
     * Renders sub view.
     * @param string $viewClassName
     * @param Model $model
     * @return string
     */
    protected function renderSubView($viewClassName, $model)
    {
        //Passing form as we have removed renderBegin from sub view that $this->form is null for the sub views
        $view = new $viewClassName(['model' => $model, 'form' => $this->form]);
        return $view->render();
    }
    
    /**
     * @inheritdoc
     */
    protected function getTabs()
    {
        return [     
                     'user'         => ['label'   => UsniAdaptor::t('application', 'General'),
                                        'content' => $this->renderTabElements('user')],
                     'person'       => ['label'   => Person::getLabel(1),
                                        'content' => $this->renderTabElements('person')],
                     'address'      => ['label'   => Address::getLabel(1),
                                        'content' => $this->renderTabElements('address')],
               ];
    }

    /**
     * @inheritdoc
     */
    protected function getTabElementsMap()
    {
        return [
                    'user'      => ['user'],
                    'person'    => ['person'],
                    'address'   => ['address']
               ];
    }
    
    /**
     * @inheritdoc
     */
    public function isMultiPartFormData()
    {
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function renderErrorSummary()
    {
        $errors = $this->getModelErrors();
        $this->model->addErrors($errors);
        return $this->form->errorSummary($this->model, ['class' => 'alert alert-danger']);
    }
    
    /**
     * Get model errors
     * @return array
     */
    protected function getModelErrors()
    {
        return ArrayUtil::merge($this->model->user->getErrors(), $this->model->person->getErrors(), $this->model->address->getErrors());
    }
    
    /**
     * @inheritdoc
     */
    protected function getButtonsWrapper()
    {
        return "<div class='form-actions text-right' style='margin-top:10px;'>{buttons}</div>";
    }
}
?>