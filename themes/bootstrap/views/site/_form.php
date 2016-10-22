<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<?php 
echo $errorSummary;
echo $callOut;
?>
<div class='panel panel-default'>
    <div class='panel-heading'>
        <h6 class='panel-title'><?php echo $title;?></h6>
    </div>
    <?php echo $description;?>
    <div class='panel-body'>
        <?php echo $begin;?>
        <?php echo $elements;?>
        <?php echo $buttons;?>
        <?php echo $end;?>
    </div>
</div>