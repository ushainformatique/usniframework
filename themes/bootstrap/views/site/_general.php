<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<div class='row'>
    <div class='col-xs-12'>
        <div class='panel panel-content'>
            <div class='panel-heading'>
                <div class='panel-title'><?php echo $title;?></div>
            </div>
            <div class='panel-body'><?php echo $content;?></div>
            <?php
            if($footer != '')
            {
            ?>
                <div class='panel-footer'><?php echo $footer;?></div>
            <?php
            }
            ?>
        </div>
    </div>
</div>