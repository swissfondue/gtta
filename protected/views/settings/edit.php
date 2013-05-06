<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('timezone')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_timezone"><?php echo Yii::t('app', 'Time Zone'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="SettingsEditForm_timezone" name="SettingsEditForm[timezone]">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach (TimeZones::$zones as $zone => $description): ?>
                        <option value="<?php echo $zone; ?>" <?php if ($zone == $model->timezone) echo 'selected'; ?>><?php echo CHtml::encode($description); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('timezone')): ?>
                    <p class="help-block"><?php echo $model->getError('timezone'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
