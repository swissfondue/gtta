<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form id="object-selection-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" data-object-list-url="<?php echo $this->createUrl('app/objectlist'); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <?php $typeNames = ReportTemplate::getValidTypeNames(); ?>

    <fieldset>
        <div class="control-group" id="template-list">
            <label class="control-label" for="ProjectReportForm_templateId"><?php echo Yii::t("app", "Template"); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectReportForm_templateId" name="ProjectReportForm[templateId]" onchange="system.report.projectFormChange(this);">
                    <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>
                    <?php foreach ($templates as $template): ?>
                        <option value="<?php echo $template->id; ?>" data-type="<?php echo $template->type; ?>"><?php echo CHtml::encode($template->localizedName); ?> (<?php echo $typeNames[$template->type]; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="rtf-report hide">
            <div class="control-group <?php if ($model->getError('fontSize')) echo 'error'; ?>">
                <label class="control-label" for="ProjectReportForm_fontSize"><?php echo Yii::t('app', 'Font Size'); ?></label>
                <div class="controls">
                    <input type="text" class="input-xlarge" id="ProjectReportForm_fontSize" name="ProjectReportForm[fontSize]" value="<?php echo $model->fontSize ? CHtml::encode($model->fontSize) : Yii::app()->params['reports']['fontSize']; ?>">
                    <?php if ($model->getError('fontSize')): ?>
                        <p class="help-block"><?php echo $model->getError('fontSize'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group <?php if ($model->getError('fontFamily')) echo 'error'; ?>">
                <label class="control-label" for="ProjectReportForm_fontFamily"><?php echo Yii::t('app', 'Font Family'); ?></label>
                <div class="controls">
                    <select class="input-xlarge" id="ProjectReportForm_fontFamily" name="ProjectReportForm[fontFamily]">
                        <?php foreach (Yii::app()->params['reports']['fonts'] as $font): ?>
                            <option value="<?php echo $font; ?>"<?php if ($font == Yii::app()->params['reports']['font']) echo 'selected'; ?>><?php echo $font; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($model->getError('fontFamily')): ?>
                        <p class="help-block"><?php echo $model->getError('fontFamily'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group <?php if ($model->getError('pageMargin')) echo 'error'; ?>">
                <label class="control-label" for="ProjectReportForm_pageMargin"><?php echo Yii::t('app', 'Page Margin'); ?></label>
                <div class="controls">
                    <input type="text" class="input-xlarge" id="ProjectReportForm_pageMargin" name="ProjectReportForm[pageMargin]" value="<?php echo $model->pageMargin ? CHtml::encode($model->pageMargin) : Yii::app()->params['reports']['pageMargin']; ?>">
                    <?php if ($model->getError('pageMargin')): ?>
                        <p class="help-block"><?php echo $model->getError('pageMargin'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group <?php if ($model->getError('cellPadding')) echo 'error'; ?>">
                <label class="control-label" for="ProjectReportForm_cellPadding"><?php echo Yii::t('app', 'Cell Padding'); ?></label>
                <div class="controls">
                    <input type="text" class="input-xlarge" id="ProjectReportForm_cellPadding" name="ProjectReportForm[cellPadding]" value="<?php echo $model->cellPadding ? CHtml::encode($model->cellPadding) : Yii::app()->params['reports']['cellPadding']; ?>">
                    <?php if ($model->getError('cellPadding')): ?>
                        <p class="help-block"><?php echo $model->getError('cellPadding'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group" id="info-checks-location-list">
                <label class="control-label" for="ProjectReportForm_infoChecksLocation"><?php echo Yii::t('app', 'Info Checks Location'); ?></label>
                <div class="controls">
                    <select class="input-xlarge" id="ProjectReportForm_infoChecksLocation" name="ProjectReportForm[infoChecksLocation]" onchange="system.report.projectFormChange(this);">
                        <?php foreach ($infoChecksLocation as $id => $loc): ?>
                            <option value="<?php echo $id; ?>"><?php echo $loc; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="control-group" id="options">
                <label class="control-label"><?php echo Yii::t('app', 'Options'); ?></label>
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" id="ProjectReportForm_options_title" name="ProjectReportForm[options][]" value="title" onchange="system.report.projectFormChange(this);" checked>
                        <?php echo Yii::t('app', 'Title Page'); ?>
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" id="ProjectReportForm_options_intro" name="ProjectReportForm[options][]" value="intro" onchange="system.report.projectFormChange(this);" checked>
                        <?php echo Yii::t('app', 'Introduction'); ?>
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" id="ProjectReportForm_options_summary" name="ProjectReportForm[options][]" value="summary" onchange="system.report.projectFormChange(this);" checked>
                        <?php echo Yii::t('app', 'Summary Block'); ?>
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" id="ProjectReportForm_options_fulfillment" name="ProjectReportForm[options][]" value="fulfillment" onchange="system.report.projectFormChange(this);" checked>
                        <?php echo Yii::t('app', 'Degree of Fulfillment Section'); ?>
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" id="ProjectReportForm_options_matrix" name="ProjectReportForm[options][]" value="matrix" onchange="system.report.projectFormChange(this);" checked>
                        <?php echo Yii::t('app', 'Risk Matrix Section'); ?>
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" id="ProjectReportForm_options_vulns" name="ProjectReportForm[options][]" value="vulns" onchange="system.report.projectFormChange(this);" checked>
                        <?php echo Yii::t('app', 'Reduced Vulnerability List'); ?>
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" id="ProjectReportForm_options_appendix" name="ProjectReportForm[options][]" value="appendix" onchange="system.report.projectFormChange(this);" checked>
                        <?php echo Yii::t('app', 'Appendix Section'); ?>
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" id="ProjectReportForm_options_attachments" name="ProjectReportForm[options][]" value="attachments" onchange="system.report.projectFormChange(this);" checked>
                        <?php echo Yii::t('app', 'Attachments Section'); ?>
                    </label>
                </div>
            </div>

            <div class="control-group" id="risk-template-list">
                <label class="control-label" for="RiskMatrixForm_templateId"><?php echo Yii::t('app', 'Risk Matrix Template'); ?></label>
                <div class="controls">
                    <select class="input-xlarge" id="RiskMatrixForm_templateId" name="RiskMatrixForm[templateId]" onchange="system.report.projectFormChange(this);">
                        <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                        <?php foreach ($riskTemplates as $template): ?>
                            <option value="<?php echo $template->id; ?>"><?php echo CHtml::encode($template->localizedName); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="help-block hide"><?php echo Yii::t('app', 'This template has no categories.'); ?></p>
                </div>
            </div>

            <div class="control-group" id="file-type">
                <label class="control-label" for="ProjectReportForm_fileType"><?php echo Yii::t('app', 'Download File Type'); ?></label>
                <div class="controls">
                    <label class="radio">
                        <input type="radio" name="ProjectReportForm[fileType]" value="<?php echo ProjectReportForm::FILE_TYPE_RTF; ?>" checked="checked" >
                        <?php echo Yii::t('app', 'RTF'); ?>
                    </label>
                    <label class="radio">
                        <input type="radio" name="ProjectReportForm[fileType]" value="<?php echo ProjectReportForm::FILE_TYPE_ZIP; ?>" >
                        <?php echo Yii::t('app', 'RTF + Attachments'); ?>
                    </label>
                </div>
            </div>
        </div>

        <div class="control-group" id="client-list">
            <label class="control-label" for="ProjectReportForm_clientId"><?php echo Yii::t('app', 'Client'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectReportForm_clientId" name="ProjectReportForm[clientId]" onchange="system.report.projectFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client->id; ?>"><?php echo CHtml::encode($client->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="help-block hide"><?php echo Yii::t('app', 'This client has no projects.'); ?></p>
            </div>
        </div>

        <div class="hide control-group" id="project-list">
            <label class="control-label" for="ProjectReportForm_projectId"><?php echo Yii::t('app', 'Project'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectReportForm_projectId" name="ProjectReportForm[projectId]" onchange="system.report.projectFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                </select>
                <p class="help-block hide"><?php echo Yii::t('app', 'This project has no targets.'); ?></p>
            </div>
        </div>

        <div class="hide control-group" id="target-list">
            <label class="control-label"><?php echo Yii::t('app', 'Targets'); ?></label>
            <div class="controls">
                <ul class="report-target-list">
                </ul>
            </div>
        </div>

        <div id="check-list" class="hide">
            <hr>
            <div class="container">
                <div class="row">
                    <div class="span8">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn" disabled><?php echo Yii::t('app', 'Generate'); ?></button>
        </div>
    </fieldset>
</form>