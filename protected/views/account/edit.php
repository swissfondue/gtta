<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('email')) echo 'error'; ?>">
            <label class="control-label" for="AccountEditForm_email"><?php echo Yii::t('app', 'E-mail'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="AccountEditForm_email" name="AccountEditForm[email]" value="<?php echo CHtml::encode($model->email); ?>">
                <?php if ($model->getError('email')): ?>
                    <p class="help-block"><?php echo $model->getError('email'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
            <label class="control-label" for="AccountEditForm_name"><?php echo Yii::t('app', 'Name'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="AccountEditForm_name" name="AccountEditForm[name]" value="<?php echo CHtml::encode($model->name); ?>">
                <?php if ($model->getError('name')): ?>
                    <p class="help-block"><?php echo $model->getError('name'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('password')) echo 'error'; ?>">
            <label class="control-label" for="AccountEditForm_password"><?php echo Yii::t('app', 'Password'); ?></label>
            <div class="controls">
                <input type="password" class="input-xlarge" id="AccountEditForm_password" name="AccountEditForm[password]">
                <?php if ($model->getError('password')): ?>
                    <p class="help-block"><?php echo $model->getError('password'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('passwordConfirmation')) echo 'error'; ?>">
            <label class="control-label" for="AccountEditForm_passwordConfirmation"><?php echo Yii::t('app', 'Password Confirmation'); ?></label>
            <div class="controls">
                <input type="password" class="input-xlarge" id="AccountEditForm_passwordConfirmation" name="AccountEditForm[passwordConfirmation]">
                <?php if ($model->getError('passwordConfirmation')): ?>
                    <p class="help-block"><?php echo $model->getError('passwordConfirmation'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('sessionDuration')) echo 'error'; ?>">
            <label class="control-label" for="AccountEditForm_sessionDuration"><?php echo Yii::t('app', 'Session Duration'); ?></label>
            <div class="controls">
                <input type="text" class="input-large" id="AccountEditForm_sessionDuration" name="AccountEditForm[sessionDuration]" value="<?= $model->sessionDuration ?>">&nbsp;<?= Yii::t("app", "Hour(s)") ?>
                <?php if ($model->getError('sessionDuration')): ?>
                    <p class="help-block"><?php echo $model->getError('sessionDuration'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (Yii::app()->user->role != User::ROLE_CLIENT): ?>
            <div class="control-group">
                <label class="control-label" for="AccountEditForm_sendNotifications"><?php echo Yii::t('app', 'Send Notifications'); ?></label>
                <div class="controls">
                    <input type="checkbox" id="AccountEditForm_sendNotifications" name="AccountEditForm[sendNotifications]" value="1" <?php if ($model->sendNotifications) echo 'checked="checked"'; ?>>
                </div>
            </div>
        <?php endif; ?>

        <div class="control-group">
            <label class="control-label"><?php echo Yii::t('app', 'Current Certificate'); ?></label>
            <div class="controls form-text">
                <?php if ($user->certificate_serial): ?>
                    <?php echo $user->certificate_serial; ?>
                <?php else: ?>
                    <?php echo Yii::t('app', 'N/A'); ?>
                <?php endif; ?>
                <a href="<?php echo $this->createUrl('account/certificate'); ?>" title="<?php echo Yii::t('app', 'Generate New'); ?>"><i class="icon icon-refresh"></i></a>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="AccountEditForm_certificateRequired"><?php echo Yii::t('app', 'Certificate Required'); ?></label>
            <div class="controls">
                <input type="checkbox" id="AccountEditForm_certificateRequired" name="AccountEditForm[certificateRequired]" value="1" <?php if ($model->certificateRequired) echo 'checked="checked"'; ?>>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
