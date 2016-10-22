<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
use usni\UsniAdaptor;
use usni\library\components\UiHtml;
use usni\library\components\UiActiveForm;
use usni\fontawesome\FA;
?>
<div class="login-wrapper">
    	<?php $form = UiActiveForm::begin(['id' => 'login-form']);?>
			<div class="popup-header">
                <span class="text-semibold"><?php echo UsniAdaptor::t('application', 'CPanel Login');?></span>
			</div>
			<div class="well">
                <?php echo $form->field($model, 'username', ['options' => ['class' => 'form-group'],
                                                             'template' => '{beginLabel}{labelTitle}{endLabel}{beginWrapper}{input}{error}{endWrapper}']);?>
                <?php echo $form->field($model, 'password', ['options' => ['class' => 'form-group'],
                                                             'template' => '{beginLabel}{labelTitle}{endLabel}{beginWrapper}{input}{error}{endWrapper}'])->passwordInput();?>
				<div class="row form-actions">
					<div class="col-xs-6">
						<div class="checkbox checkbox-admin">
                            <?php echo UiHtml::activeCheckbox($model, 'rememberMe', ['class' => 'checked']);?>
						</div>
					</div>

					<div class="col-xs-6">
                        <button type="submit" class="btn btn-warning pull-right"><?php echo FA::icon('bars')->size('lg');?>
 <?php echo UsniAdaptor::t('users', 'Sign In');?></button>
					</div>
				</div>
			</div>
    	<?php UiActiveForm::end();?>
</div>