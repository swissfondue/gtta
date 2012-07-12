<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form id="project-report-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" data-project-url="<?php echo $this->createUrl('report/projectlist'); ?>" data-target-url="<?php echo $this->createUrl('report/targetlist'); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group">
            <label class="control-label" for="ProjectReportForm_clientId"><?php echo Yii::t('app', 'Client'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectReportForm_clientId" name="ProjectReportForm[clientId]" onchange="user.report.projectFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client->id; ?>"><?php echo CHtml::encode($client->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="hide control-group" id="project-list">
            <label class="control-label" for="ProjectReportForm_projectId"><?php echo Yii::t('app', 'Project'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectReportForm_projectId" name="ProjectReportForm[projectId]" onchange="user.report.projectFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                </select>
            </div>
        </div>

        <div class="hide control-group" id="target-list">
            <label class="control-label"><?php echo Yii::t('app', 'Targets'); ?></label>
            <div class="controls">
                <ul class="report-target-list">
                </ul>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn" disabled><?php echo Yii::t('app', 'Generate'); ?></button>
        </div>
    </fieldset>
</form>