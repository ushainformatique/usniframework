<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
use usni\UsniAdaptor;
use usni\library\components\UiHtml;
?>
<div class="page-header">
    <div class="page-title">
        <h3><?php echo UsniAdaptor::t('application', 'Installation');?></h3>
    </div>
</div>
<div class='panel panel-default panel-install'>
    <div class="panel-heading">
        <h6 class="panel-title">
            <?php echo UsniAdaptor::t('install', 'Pre-Installation Check'); ?>
        </h6>
    </div>
    <div class='panel-body'>
    <?php
    echo UsniAdaptor::app()->controller->renderPartial('@usni/themes/bootstrap/views/install/_systemconf', 
                                                    array('requirements' => $requirements,
                                                          'summary'      => $summary));
    ?>
        <br/>
            <div class='form-actions text-right'>
                <?php
                if ($summary['errors'] == 0)
                {
                    echo UiHtml::a(UsniAdaptor::t('application', 'Continue'), UsniAdaptor::createUrl('/install/default/settings'), array('class' => 'btn btn-success'));
                }
                else
                {
                    echo UiHtml::a(UsniAdaptor::t('application', 'Recheck'), UsniAdaptor::createUrl('/install/default/settings'), array('class' => 'btn btn-danger'));
                }
                ?>
            </div>
    </div>
</div>

