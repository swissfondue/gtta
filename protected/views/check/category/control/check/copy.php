<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" id="object-selection-form" data-object-list-url="<?php echo $this->createUrl('app/objectlist'); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group">
            <label class="control-label" for="CheckCopyForm_controlId"><?php echo Yii::t('app', 'Control'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="CheckCopyForm_controlId" onchange="admin.check.loadChecks($(this), $('#CheckCopyForm_id'));">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($categories as $cat): ?>
                        <?php foreach ($cat->controls as $ctrl): ?>
                            <option value="<?php echo $ctrl->id; ?>" <?php if ($control->id == $ctrl->id) echo "selected"; ?>><?php echo CHtml::encode($cat->localizedName); ?> / <?php echo CHtml::encode($ctrl->localizedName); ?></option>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('id')) echo 'error'; ?>" id="check-selector">
            <label class="control-label" for="CheckCopyForm_id"><?php echo Yii::t('app', 'Check'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="CheckCopyForm_id" name="CheckCopyForm[id]">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($checks as $check): ?>
                        <option value="<?php echo $check->id; ?>"><?php echo CHtml::encode($check->localizedName); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('id')): ?>
                    <p class="help-block"><?php echo $model->getError('id'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
