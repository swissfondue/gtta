<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form id="TargetImportForm" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" value="1" name="TargetImportForm[proceed]">

    <fieldset>
        <div class="control-group <?php if ($model->getError('file')) echo 'error'; ?>">
            <label class="control-label" for="TargetImportForm_file"><?php echo Yii::t('app', 'File'); ?></label>
            <div class="controls">
                <input type="file" id="TargetImportForm_file" name="TargetImportForm[file]">
                <?php if ($model->getError('file')): ?>
                    <p class="help-block"><?php echo $model->getError('file'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button id="restore" type="submit" class="btn"><?php echo Yii::t('app', 'Import'); ?></button>
        </div>
    </fieldset>
</form>