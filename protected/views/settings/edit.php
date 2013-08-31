<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($form->getError('workstationId')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_workstationId"><?php echo Yii::t('app', 'Workstation ID'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="SettingsEditForm_workstationId" name="SettingsEditForm[workstationId]" value="<?php echo CHtml::encode($form->workstationId); ?>">
                <?php if ($form->getError('workstationId')): ?>
                    <p class="help-block"><?php echo $form->getError('workstationId'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($form->getError('workstationKey')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_workstationKey"><?php echo Yii::t('app', 'Workstation Key'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="SettingsEditForm_workstationKey" name="SettingsEditForm[workstationKey]" value="<?php echo CHtml::encode($form->workstationKey); ?>">
                <?php if ($form->getError('workstationKey')): ?>
                    <p class="help-block"><?php echo $form->getError('workstationKey'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($form->getError('timezone')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_timezone"><?php echo Yii::t('app', 'Time Zone'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="SettingsEditForm_timezone" name="SettingsEditForm[timezone]">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach (TimeZones::$zones as $zone => $description): ?>
                        <option value="<?php echo $zone; ?>" <?php if ($zone == $form->timezone) echo 'selected'; ?>><?php echo CHtml::encode($description); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($form->getError('timezone')): ?>
                    <p class="help-block"><?php echo $form->getError('timezone'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
