<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
use usni\library\utils\FlashUtil;
use usni\UsniAdaptor;
use usni\library\modules\auth\managers\AuthManager;
use usni\library\modules\service\utils\ServicePermissionUtil;
?>
<div class='row'>
    <div class='col-xs-12'>
        <?php
        echo FlashUtil::render('serviceexecutionsuccess', 'alert alert-success');
        echo FlashUtil::render('serviceexecutionfailure', 'alert alert-warning');
        echo FlashUtil::render('resetuserpermissions', 'alert alert-success');
        ?>
        <div class='panel panel-default'>
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $title; ?></h3>
            </div>
            <div class="panel-body">
                <?php echo UsniAdaptor::t('service', 'The list of services provided below are at the system level so before running them, make sure they are not going to effect your current set up.'); ?>
            </div>
            <table class="table">
                <tbody>
                    <?php
                    if(UsniAdaptor::app()->user->isSuperUser() || AuthManager::isUserInAdministrativeGroup(UsniAdaptor::app()->user->getUserModel()))
                    {
                        /*echo ServicePermissionUtil::renderLinkOnIndexPageByPermission(getLabel('application', 'runmigration'),
                                                                                          createUrl('service/default/migrate'),
                                                                                          UsniAdaptor::app()->user->getUserModel(),
                                                                                          'service.migrate');*/
                        echo ServicePermissionUtil::renderLinkOnIndexPageByPermission(UsniAdaptor::t('service', 'System Configuration'),
                                                                                      UsniAdaptor::createUrl('service/default/check-system'),
                                                                                      UsniAdaptor::app()->user->getUserModel(),
                                                                                      'service.checksystem');
                        echo ServicePermissionUtil::renderLinkOnIndexPageByPermission(UsniAdaptor::t('auth', 'Rebuild Permissions'),
                                                                                      UsniAdaptor::createUrl('service/default/load-modules-permissions'),
                                                                                      UsniAdaptor::app()->user->getUserModel(),
                                                                                      'service.loadmodulespermissions');
                        echo ServicePermissionUtil::renderLinkOnIndexPageByPermission(UsniAdaptor::t('auth', 'Rebuild Module Metadata'),
                                                                                      UsniAdaptor::createUrl('service/default/rebuild-module-metadata'),
                                                                                      UsniAdaptor::app()->user->getUserModel(),
                                                                                      'service.rebuildmodulemetadata');
                    }
                    echo ServicePermissionUtil::renderLinkOnIndexPageByPermission(UsniAdaptor::t('application', 'Clear Cache'),
                                                                                  UsniAdaptor::createUrl('service/default/index', array('clearCache' => 'true')),
                                                                                  UsniAdaptor::app()->user->getUserModel(),
                                                                                  'access.service');
                    echo ServicePermissionUtil::renderLinkOnIndexPageByPermission(UsniAdaptor::t('application', 'Clear Assets'),
                                                                                  UsniAdaptor::createUrl('service/default/clear-assets'),
                                                                                  UsniAdaptor::app()->user->getUserModel(),
                                                                                  'access.service');
                    echo ServicePermissionUtil::renderLinkOnIndexPageByPermission(UsniAdaptor::t('auth', 'Reset user permissions'),
                                                                                  UsniAdaptor::createUrl('service/default/reset-user-permissions'),
                                                                                  UsniAdaptor::app()->user->getUserModel(),
                                                                                  'service.resetuserpermissions');
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>