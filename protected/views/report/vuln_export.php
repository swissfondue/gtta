<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form id="object-selection-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" data-object-list-url="<?php echo $this->createUrl('app/objectlist'); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group" id="client-list">
            <label class="control-label" for="VulnExportReportForm_clientId"><?php echo Yii::t('app', 'Client'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="VulnExportReportForm_clientId" name="VulnExportReportForm[clientId]" onchange="system.report.vulnExportFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client->id; ?>"><?php echo CHtml::encode($client->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="help-block hide"><?php echo Yii::t('app', 'This client has no projects.'); ?></p>
            </div>
        </div>

        <div class="hide control-group" id="project-list">
            <label class="control-label" for="VulnExportReportForm_projectId"><?php echo Yii::t('app', 'Project'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="VulnExportReportForm_projectId" name="VulnExportReportForm[projectId]" onchange="system.report.vulnExportFormChange(this);">
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

        <div class="hide" id="report-details">
            <div class="control-group">
                <label class="control-label" for="VulnExportReportForm_header"><?php echo Yii::t('app', 'Show Header'); ?></label>
                <div class="controls">
                    <input type="checkbox" id="VulnExportReportForm_header" name="VulnExportReportForm[header]" value="1" checked onchange="system.report.vulnExportFormChange(this);">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"><?php echo Yii::t('app', 'Ratings'); ?></label>
                <div class="controls">
                    <?php foreach ($ratings as $rating => $name): ?>
                        <label class="checkbox">
                            <input type="checkbox" id="VulnExportReportForm_ratings_<?php echo $rating; ?>" name="VulnExportReportForm[ratings][]" value="<?php echo $rating; ?>" checked onchange="system.report.vulnExportFormChange(this);">
                            <?php echo $name; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"><?php echo Yii::t('app', 'Columns'); ?></label>
                <div class="controls">
                    <?php foreach ($columns as $column => $name): ?>
                        <label class="checkbox">
                            <input type="checkbox" id="VulnExportReportForm_columns_<?php echo $column; ?>" name="VulnExportReportForm[columns][]" value="<?php echo $column; ?>" checked onchange="system.report.vulnExportFormChange(this);">
                            <?php echo $name; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn" disabled><?php echo Yii::t('app', 'Generate'); ?></button>
        </div>
    </fieldset>
</form>