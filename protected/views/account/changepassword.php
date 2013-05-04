<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('password')) echo 'error'; ?>">
            <label class="control-label" for="AccountRestoreForm_password"><?php echo Yii::t('app', 'New Password'); ?></label>
            <div class="controls">
                <input type="password" class="input-xlarge" id="AccountRestoreForm_password" name="AccountRestoreForm[password]">
                <?php if ($model->getError('password')): ?>
                    <p class="help-block"><?php echo $model->getError('password'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('passwordConfirmation')) echo 'error'; ?>">
            <label class="control-label" for="AccountRestoreForm_passwordConfirmation"><?php echo Yii::t('app', 'Password Confirmation'); ?></label>
            <div class="controls">
                <input type="password" class="input-xlarge" id="AccountRestoreForm_passwordConfirmation" name="AccountRestoreForm[passwordConfirmation]">
                <?php if ($model->getError('passwordConfirmation')): ?>
                    <p class="help-block"><?php echo $model->getError('passwordConfirmation'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Change'); ?></button>
        </div>
    </fieldset>
</form>
