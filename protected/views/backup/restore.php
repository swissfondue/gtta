<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form id="RestoreForm" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" value="1" name="RestoreForm[proceed]">

    <fieldset>
        <div class="control-group <?php if ($model->getError('backup')) echo 'error'; ?>">
            <label class="control-label" for="RestoreForm_backup"><?php echo Yii::t('app', 'Backup File'); ?></label>
            <div class="controls">
                <input type="file" id="RestoreForm_backup" name="RestoreForm[backup]">
                <?php if ($model->getError('backup')): ?>
                    <p class="help-block"><?php echo $model->getError('backup'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button id="restore" type="submit" class="btn"><?php echo Yii::t('app', 'Restore'); ?></button>
        </div>
    </fieldset>
</form>

<?php if ($restoring): ?>
    <script>
        $('#restore').button('loading');

        setTimeout(function () {
            admin.backup.check('<?php echo $this->createUrl("backup/check", array( "action" => "restore" )); ?>', "restore");
        }, admin.backup.checkTimeout);
    </script>
<?php endif; ?>