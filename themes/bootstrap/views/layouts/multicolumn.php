<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
echo $topnav;
?>
<!-- Page container -->
<div class="page-container">
    <?php echo $leftnav; ?>
    <div class="page-content">
        <?php
        echo $breadcrumbs;
        echo $content;
        //echo $bottombar;
        echo $footer;
        ?>
    </div>
    <?php echo $rightnav; ?>
</div>