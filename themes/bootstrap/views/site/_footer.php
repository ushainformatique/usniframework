<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
use usni\UsniAdaptor;
?>
<div class="footer clearfix">
    <div class="pull-left">
        Copyright &copy; <?php echo date('Y'); ?> <?php echo UsniAdaptor::app()->name;?>. All Rights Reserved.
    </div>
    <div class="pull-right">
        <?php echo UsniAdaptor::app()->powered(); ?>
    </div>
</div><!-- footer -->
