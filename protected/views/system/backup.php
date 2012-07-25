<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<?php if ($runningChecks): ?>
    <div class="form-description">
        <?php echo Yii::t('app', 'Some automated checks are in progress. Please stop all checks before backing up the system.'); ?>
    </div>
<?php endif; ?>

<form id="SystemBackupForm" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" value="1" name="SystemBackupForm[proceed]">

    <fieldset>
        <div class="control-group">
            <label class="control-label"><?php echo Yii::t('app', 'Last Backup'); ?></label>
            <div class="controls form-text">
                <?php echo $backedUp; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn" <?php if ($runningChecks) echo 'disabled'; ?>><?php echo Yii::t('app', 'Backup'); ?></button>
        </div>
    </fieldset>
</form>