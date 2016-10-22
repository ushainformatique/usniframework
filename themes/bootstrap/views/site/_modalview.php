<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<div class="modal fade in" id="<?php echo $modalId;?>" tabindex="-1" role="dialog" aria-labelledby="screenOptionsLabel" aria-hidden="true">
    <div class="modal-dialog <?php echo $size;?>">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="modal-btn-close">&times;</button>
                <h4 class="modal-title"><?php echo $title;?></h4>
            </div>
            <div class="modal-body with-padding"><?php echo $body;?></div>
            <?php
            if(!empty($footer))
            {
            ?>
                <div class="modal-footer">
                    <?php echo $footer;?>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>