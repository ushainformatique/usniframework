<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\views;

use usni\library\views\UiDetailView;
/**
 * View for displaying user profile.
 * @package usni\library\modules\users\views
 */
class UserProfileView extends UiDetailView
{
    /**
     * Get columns.
     * @return array
     */
    public function getColumns()
    {
        return [
                    ['attribute' => 'profile_image', 'value' => $this->model->getProfileImage(), 'format' => 'raw'],
                    'fullName',
                    'mobilephone',
                    'email'
                ];
    }

    /**
     * Should title be rendered
     * @return boolean
     */
    protected function shouldRenderTitle()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected static function shouldRenderCreatedAttributes()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected static function shouldRenderModifiedAttributes()
    {
        return false;
    }
    
    /**
     * @inheritdoc
     */
    protected function renderDetailModelBrowseView()
    {
        return null;
    }
}
?>