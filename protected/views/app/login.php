<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo.png" class="login-logo">

<div class="login-block">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

    <hr>

    <form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
        <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

        <fieldset>
            <div class="control-group <?php if ($model->getError('email')) echo 'error'; ?>">
                <label class="control-label" for="LoginForm_email"><?php echo Yii::t('app', 'E-mail'); ?></label>
                <div class="controls">
                    <input type="text" class="input-xlarge" id="LoginForm_email" name="LoginForm[email]" value="<?php echo CHtml::encode($model->email); ?>">
                    <?php if ($model->getError('email')): ?>
                        <p class="help-block"><?php echo $model->getError('email'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group <?php if ($model->getError('password')) echo 'error'; ?>">
                <label class="control-label" for="LoginForm_password"><?php echo Yii::t('app', 'Password'); ?></label>
                <div class="controls">
                    <input type="password" class="input-xlarge" id="LoginForm_password" name="LoginForm[password]">
                    <?php if ($model->getError('password')): ?>
                        <p class="help-block"><?php echo $model->getError('password'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn"><?php echo Yii::t('app', 'Login'); ?></button>&nbsp;
                    <a class="btn" href="<?php echo $this->createUrl("account/restore"); ?>"><?php echo Yii::t("app", "Restore Account"); ?></a>
                </div>
            </div>
        </fieldset>
    </form>
</div>