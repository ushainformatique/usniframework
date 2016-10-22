<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\widgets;

use usni\library\components\UiHtml;
use usni\UsniAdaptor;
use yii\grid\DataColumn;
use usni\library\utils\MetadataUtil;
use usni\library\components\UiGridView;
/**
 * Extends @GridView to render the grid using bootstrap.
 * @package usni\library\extensions\bootstrap\widgets
 */
class UiGridViewWidget extends \yii\grid\GridView
{
    /**
     * If detail view would be modal.
     * @var boolean
     */
    public $modalDetailView = true;

    /**
     * Layout for the grid view.
     * @var string
     */
    public $layout = "{caption}\n<div class='panel panel-default'>{summary}\n{items}\n{pager}</div>";

    /**
     * View within which the grid view would be rendered
     * @var UiGridView
     */
    public $owner;

    /**
     * Renders the table header.
     * @return string the rendering result.
     */
    public function renderTableHeader()
    {
        return parent::renderTableHeader();
    }

    /**
     * @inheritdoc
     */
    public function renderItems()
    {
        $columnGroup = $this->renderColumnGroup();
        $tableHeader = $this->showHeader ? $this->renderTableHeader() : false;
        $tableBody = $this->renderTableBody();
        $tableFooter = $this->showFooter ? $this->renderTableFooter() : false;
        $content = array_filter([
            $columnGroup,
            $tableHeader,
            $tableFooter,
            $tableBody,
        ]);
        return UiHtml::tag('table', implode("\n", $content), $this->tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function renderSection($name)
    {
        switch ($name)
        {
            case "{caption}":
                return $this->renderCaption();
            default:
                return parent::renderSection($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function renderCaption()
    {
        if (!empty($this->caption))
        {
            return UiHtml::tag('h6', $this->caption, $this->captionOptions);
        }
        else
        {
            return parent::renderCaption();
        }
    }

    /**
     * Creates column objects and initializes them.
     */
    protected function initColumns()
    {
        parent::initColumns();
        $cells = [];
        foreach ($this->columns as $index => $column)
        {
            if ($column instanceof DataColumn)
            {
                /* @var $column Column */
                $cells[] = strip_tags($column->renderHeaderCell());
                $column->sortLinkOptions = ['class' => 'sorting'];
                $this->columns[$index] = $column;
            }
        }
        $ownerClassName = UsniAdaptor::getObjectClassName($this->owner);
        $this->columns  = MetadataUtil::getGridColumnsByDisplayOrder($this->columns, $ownerClassName, $cells);
    }
}
?>