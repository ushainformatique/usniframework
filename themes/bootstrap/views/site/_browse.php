<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
use usni\UsniAdaptor;
?>

<div class='panel panel-default'>
    <div class="panel-body form-horizontal">
        <div class="form-group">
            <label class="control-label col-sm-2 text-right"><?php echo UsniAdaptor::t('application', 'Browse');?></label>
            <div class="col-sm-10"><?php echo $content;?></div>
        </div>
    </div>
</div>
