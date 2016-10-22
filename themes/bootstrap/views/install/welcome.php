<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
use usni\library\components\UiHtml;
use usni\UsniAdaptor;
?>
<div class="page-header">
    <div class="page-title">
        <h3><?php echo UsniAdaptor::t('application', 'Installation');?></h3>
    </div>
</div>
<div class='panel panel-default panel-install'>
    <div class="panel-heading">
        <h6 class="panel-title">
            <?php echo UsniAdaptor::t('application', 'Welcome to application setup'); ?>
        </h6>
    </div>
    <div class='panel-body'>
            <?php echo UsniAdaptor::t('install', 'Application Setup'); ?>
            <div class='form-actions text-right'>
                    <?php echo UiHtml::a(UsniAdaptor::t('application', 'Continue'),
                                                UsniAdaptor::createUrl('/install/default/check-system'),
                                                array('class' => 'btn btn-success'));
                    ?>
            </div>
    </div>
</div>