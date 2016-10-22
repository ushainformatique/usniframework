<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
 ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="bulkEditLabel"><?php echo $title; ?></h4>
</div>
<?php echo $begin; ?>
<div class="modal-body"><?php echo $elements; ?></div>
<div class="modal-footer">
    <?php echo $buttons; ?>
</div>
<?php echo $end; ?>
        