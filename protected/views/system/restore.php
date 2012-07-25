<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<?php if ($runningChecks): ?>
    <div class="form-description">
        <?php echo Yii::t('app', 'Some automated checks are in progress. Please stop all checks before restoring the system.'); ?>
    </div>
<?php endif; ?>

<form id="SystemRestoreForm" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" value="1" name="SystemRestoreForm[proceed]">

    <fieldset>
        <div class="control-group <?php if ($model->getError('backup')) echo 'error'; ?>">
            <label class="control-label" for="SystemRestoreForm_backup"><?php echo Yii::t('app', 'Backup File'); ?></label>
            <div class="controls">
                <input type="file" id="SystemRestoreForm_backup" name="SystemRestoreForm[backup]">
                <?php if ($model->getError('backup')): ?>
                    <p class="help-block"><?php echo $model->getError('backup'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn" <?php if ($runningChecks) echo 'disabled'; ?>><?php echo Yii::t('app', 'Restore'); ?></button>
        </div>
    </fieldset>
</form>