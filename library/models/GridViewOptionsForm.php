<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\models;

use usni\library\components\UiFormModel;
use usni\UsniAdaptor;
use usni\library\components\UiGridView;

/**
 * GridViewOptionsForm is the data structure for keeping user grid view form data.
 * @package usni\library\models
 */
class GridViewOptionsForm extends UiFormModel
{
    /**
     * Stores available columns.
     * @var array
     */
    public $availableColumns = [];
    /**
     * Stores visible columns.
     * @var array
     */
    public $displayedColumns = [];
    /**
     * Stores items per page.
     * @var integer
     */
    public $itemsPerPage;
    /**
     * Stores view class name on which form would be displayed.
     * @var integer
     */
    public $viewClassName;
    /**
     * Stores if detail view should be in modal way.
     * @var boolean
     */
    public $modalDetailView;
    /**
     * Declares the validation rules.
     * @return array
     */
    public function rules()
    {
        return [
                    [['displayedColumns', 'itemsPerPage', 'viewClassName'], 'required'],
                    ['availableColumns', 'safe'],
                    ['itemsPerPage', 'number', 'min' => 1, 'integerOnly' => true],
                    ['itemsPerPage', 'default', 'value' => UiGridView::DEFAULT_LIST_SIZE],
                    ['modalDetailView', 'boolean'],
                    [['itemsPerPage', 'viewClassName', 'itemsPerPage', 'modalDetailView'], 'safe']
               ];
    }

    /**
     * Declares attribute labels.
     * @return array
     */
    public function attributeLabels()
    {
        return [
                    'displayedColumns'  => UsniAdaptor::t('application', 'Displayed Columns'),
                    'itemsPerPage'      => UsniAdaptor::t('application', 'Items Per Page'),
                    'availableColumns'  => UsniAdaptor::t('application', 'Available Columns'),
                    'modalDetailView'   => UsniAdaptor::t('application', 'Modal Detail View'),
               ];
    }
}
