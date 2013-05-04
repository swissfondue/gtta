<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<?php if ($success): ?>
    <?php echo Yii::t("app", "We've sent you an email with a link to reset your password and instructions on how to change it."); ?><br>
    <?php echo Yii::t("app", "If you won't receive the message within a few minutes, please check your spam folder."); ?>
<?php else: ?>
    <form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
        <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

        <fieldset>
            <div class="control-group <?php if ($model->getError('email')) echo 'error'; ?>">
                <label class="control-label" for="AccountRestoreForm_email"><?php echo Yii::t('app', 'E-mail'); ?></label>
                <div class="controls">
                    <input type="text" class="input-xlarge" id="AccountRestoreForm_email" name="AccountRestoreForm[email]">
                    <?php if ($model->getError('email')): ?>
                        <p class="help-block"><?php echo $model->getError('email'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn"><?php echo Yii::t('app', 'Restore'); ?></button>
            </div>
        </fieldset>
    </form>
<?php endif; ?>