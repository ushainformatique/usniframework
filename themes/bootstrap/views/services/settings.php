<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
use usni\UsniAdaptor;
?>
<div class='panel panel-default'>
    <div class="panel-heading">
        <div class="panel-title">
            <?php echo UsniAdaptor::t('service', 'System Configuration'); ?>
        </div>
    </div>
    <?php
    echo UsniAdaptor::app()->controller->renderPartial('@usni/themes/bootstrap/views/install/_systemconf', 
                                                       array('requirements' => $requirements,
                                                             'summary'      => $summary));
    ?>
</div>

