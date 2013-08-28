<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form id="BackupForm" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" value="1" name="BackupForm[proceed]">

    <fieldset>
        <div class="control-group">
            <label class="control-label"><?php echo Yii::t("app", "Last Backup"); ?></label>
            <div class="controls form-text">
                <?php echo $backedUp; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Backup"); ?></button>
        </div>
    </fieldset>
</form>