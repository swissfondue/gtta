<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form id="TargetListAddForm" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('targetList')) echo 'error'; ?>">
            <label class="control-label" for="TargetListAddForm_targetList"><?php echo Yii::t('app', 'Targets'); ?></label>
            <div class="controls">
                <textarea class="input-xlarge" rows="10" id="TargetListAddForm_targetList" name="TargetListAddForm[targetList]"><?php echo CHtml::encode($model->targetList); ?></textarea>
                <?php if ($model->getError('targetList')): ?>
                    <p class="help-block"><?php echo $model->getError('targetList'); ?></p>
                <?php else: ?>
                    <p class="help-block">
                        <?php echo Yii::t("app", "List of targets separated by newline."); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Add'); ?></button>
        </div>
    </fieldset>
</form>