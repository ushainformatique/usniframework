<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\components\UiHtml;
use usni\UsniAdaptor;
use usni\library\components\UiActiveForm;
use usni\fontawesome\FA;
use usni\library\utils\AdminUtil;
use usni\library\utils\FileUploadUtil;
/**
 * PersonEditView class file.
 * 
 * @package usni\library\modules\users\views
 */
class PersonEditView extends \usni\library\views\MultiModelEditView
{
    /**
     * @inheritdoc
     */
    public function getFormBuilderMetadata()
    {
        $elements = [
                        'firstname'     => ['type' => 'text'],
                        'lastname'      => ['type' => 'text'],
                        'email'         => ['type' => 'text'],
                        'mobilephone'   => ['type' => 'text'],
                        $this->renderThumbnail(),
                        'profile_image' => ['type' => UiActiveForm::INPUT_FILE],
                    ];
        $metadata = [
                        'elements'              => $elements,
                    ];
        return $metadata;
    }

    /**
     * Renders thumbnail.
     * @return string or null
     */
    protected function renderThumbnail()
    {
        if ($this->model->profile_image != null)
        {
            $ifImageExists = FileUploadUtil::checkIfFileExists(UsniAdaptor::app()->assetManager->imageUploadPath, $this->model->profile_image);
            if($ifImageExists)
            {
                $thumbnail  = FileUploadUtil::getThumbnailImage($this->model, 'profile_image');
                $icon       = FA::icon('trash');
                $title      = UsniAdaptor::t('application', 'Delete this image');
                $deleteLink = UiHtml::a($icon, '#', ['class' => 'delete-image', 'title' => $title]);
                return UiHtml::tag('div', $thumbnail . $deleteLink, ['class' => 'image-thumbnail']);
            }
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function isMultiPartFormData()
    {
        return true;
    }

    /**
     * Gets excluded elements by scenario.
     * @return array
     */
    public function getExcludedAttributes()
    {
        $scenario   = $this->model->scenario;
        if ($scenario == 'registration' || $scenario == 'editprofile')
        {
            return ['profile_image'];
        }
        return [];
    }
    
    /**
     * @inheritdoc
     */
    protected function registerScripts()
    {
        if($this->model->id != null)
        {
            $url    = $this->getDeleteImageUrl();
            AdminUtil::registerDeleteImageScripts($this->model->id, $url, get_class($this->model), $this->getView());
        }
    }
    
    /**
     * Delete image url
     * @return string
     */
    protected function getDeleteImageUrl()
    {
        return UsniAdaptor::createUrl('users/default/delete-image');
    }
}