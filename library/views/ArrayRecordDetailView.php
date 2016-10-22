<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

/**
 * Abstract base class to render the details if model is an array record.
 * @package usni\library\views
 */
abstract class ArrayRecordDetailView extends UiDetailView
{
    /**
     * @inheritdoc
     */
    protected function getPermissionPrefix()
    {
        throw new \usni\library\exceptions\MethodNotImplementedException();
    }
}
?>