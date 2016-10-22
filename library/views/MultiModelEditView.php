<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\library\extensions\bootstrap\views\UiBootstrapEditView;
/**
 * Abstract base class for editing the form.
 * @package usni\library\views
 */
abstract class MultiModelEditView extends UiBootstrapEditView
{
    /**
     * Resolve output data
     * @return array
     */
    public function resolveOutputData()
    {
        $metadata = $this->getFormBuilderMetadata();
        return array(
            'elements'    => $this->renderElements($metadata['elements']),
		);
    }
    
    /**
     * Renders content.
     * @return string
     */
    protected function renderContent()
    {
        $outputData = $this->resolveOutputData();
        extract($outputData);
        ob_start();
        echo $elements;
        return ob_get_clean();
    }
}
?>