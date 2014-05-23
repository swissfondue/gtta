<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.ui.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.iframe-transport.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.fileupload.js"></script>

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

        <div class="control-group">
            <label class="control-label"><?php echo Yii::t("app", "Integration Key"); ?></label>
            <div class="controls form-text">
                <span id="integration-key" data-integration-key-url="<?php echo $this->createUrl("settings/integration-key"); ?>">
                    <?php if ($this->_system->integration_key): ?>
                        <?php echo $this->_system->integration_key; ?>
                    <?php else: ?>
                        <?php echo Yii::t("app", "N/A"); ?>
                    <?php endif; ?>
                </span>

                <a href="#generate" title="<?php echo Yii::t("app", "Generate New"); ?>" onclick="admin.settings.generateIntegrationKey();"><i class="icon icon-refresh"></i></a>
            </div>
        </div>

        <hr>

        <div class="control-group">
            <label class="control-label"><?php echo Yii::t("app", "Logo"); ?></label>
            <div class="controls form-text">
                <div class="logo-image" data-control-url="<?php echo $this->createUrl("settings/controllogo"); ?>">
                    <img src="<?php echo $this->createUrl("app/logo"); ?>">
                </div>
                <div class="file-input">
                    <a href="#logo"><?php echo Yii::t("app", "Upload Logo"); ?></a>
                    <input type="file" name="SystemLogoUploadForm[image]" data-upload-url="<?php echo $this->createUrl("settings/uploadlogo"); ?>">
                </div>

                <div class="upload-message hide"><?php echo Yii::t('app', 'Uploading...'); ?></div>

                <a class="delete-logo-link <?php if (!$this->_system->logo_type) echo "hide"; ?>" href="#delete-logo" onclick="admin.settings.delLogo();"><?php echo Yii::t("app", "Delete Logo"); ?></a>
            </div>
        </div>

        <div class="control-group <?php if ($form->getError('copyright')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_copyright"><?php echo Yii::t('app', 'Copyright'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="SettingsEditForm_copyright" name="SettingsEditForm[copyright]" value="<?php echo CHtml::encode($form->copyright); ?>">
                <?php if ($form->getError('copyright')): ?>
                    <p class="help-block"><?php echo $form->getError('copyright'); ?></p>
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

        <div class="control-group <?php if ($form->getError('languageId')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_languageId"><?php echo Yii::t("app", "Default Language"); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="SettingsEditForm_languageId" name="SettingsEditForm[languageId]">
                    <?php foreach ($languages as $language): ?>
                        <option value="<?php echo $language->id; ?>" <?php if ($language->id == $system->language_id) echo "selected"; ?>><?php echo CHtml::encode($language->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($form->getError('languageId')): ?>
                    <p class="help-block"><?php echo $form->getError('languageId'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <hr>
        
        <h3><?php echo Yii::t("app", "Report Settings"); ?></h3>

        <br>
        
        <div class="control-group <?php if ($form->getError('reportLowPedestal')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_reportLowPedestal"><?php echo Yii::t('app', 'Low Risk Pedestal'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="SettingsEditForm_reportLowPedestal" name="SettingsEditForm[reportLowPedestal]" value="<?php echo CHtml::encode($form->reportLowPedestal); ?>">
                <?php if ($form->getError('reportLowPedestal')): ?>
                    <p class="help-block"><?php echo $form->getError('reportLowPedestal'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($form->getError('reportMedPedestal')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_reportMedPedestal"><?php echo Yii::t('app', 'Medium Risk Pedestal'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="SettingsEditForm_reportMedPedestal" name="SettingsEditForm[reportMedPedestal]" value="<?php echo CHtml::encode($form->reportMedPedestal); ?>">
                <?php if ($form->getError('reportMedPedestal')): ?>
                    <p class="help-block"><?php echo $form->getError('reportMedPedestal'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($form->getError('reportHighPedestal')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_reportHighPedestal"><?php echo Yii::t('app', 'High Risk Pedestal'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="SettingsEditForm_reportHighPedestal" name="SettingsEditForm[reportHighPedestal]" value="<?php echo CHtml::encode($form->reportHighPedestal); ?>">
                <?php if ($form->getError('reportHighPedestal')): ?>
                    <p class="help-block"><?php echo $form->getError('reportHighPedestal'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($form->getError('reportMaxRating')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_reportMaxRating"><?php echo Yii::t('app', 'Maximum Rating'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="SettingsEditForm_reportMaxRating" name="SettingsEditForm[reportMaxRating]" value="<?php echo CHtml::encode($form->reportMaxRating); ?>">
                <?php if ($form->getError('reportMaxRating')): ?>
                    <p class="help-block"><?php echo $form->getError('reportMaxRating'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <hr>

        <h3><?php echo Yii::t("app", "Damping Factor Settings"); ?></h3>

        <br>
        
        <div class="control-group <?php if ($form->getError('reportMedDampingLow')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_reportMedDampingLow"><?php echo Yii::t('app', 'Medium Risk Region: Low Risks'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="SettingsEditForm_reportMedDampingLow" name="SettingsEditForm[reportMedDampingLow]" value="<?php echo CHtml::encode($form->reportMedDampingLow); ?>">
                <?php if ($form->getError('reportMedDampingLow')): ?>
                    <p class="help-block"><?php echo $form->getError('reportMedDampingLow'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($form->getError('reportHighDampingLow')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_reportHighDampingLow"><?php echo Yii::t('app', 'High Risk Region: Low Risks'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="SettingsEditForm_reportHighDampingLow" name="SettingsEditForm[reportHighDampingLow]" value="<?php echo CHtml::encode($form->reportHighDampingLow); ?>">
                <?php if ($form->getError('reportHighDampingLow')): ?>
                    <p class="help-block"><?php echo $form->getError('reportHighDampingLow'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($form->getError('reportHighDampingMed')) echo 'error'; ?>">
            <label class="control-label" for="SettingsEditForm_reportHighDampingMed"><?php echo Yii::t('app', 'High Risk Region: Medium Risks'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="SettingsEditForm_reportHighDampingMed" name="SettingsEditForm[reportHighDampingMed]" value="<?php echo CHtml::encode($form->reportHighDampingMed); ?>">
                <?php if ($form->getError('reportHighDampingMed')): ?>
                    <p class="help-block"><?php echo $form->getError('reportHighDampingMed'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>

<script>
    $(function () {
        admin.settings.initLogoUploadForm();
    });
</script>