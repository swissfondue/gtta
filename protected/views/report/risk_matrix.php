<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<?php if (!$templates): ?>
    <div class="form-description">
        <?php echo Yii::t('app', 'There are no risk templates in the system. Please add some templates to generate this type of report.'); ?>
    </div>
<?php else: ?>
    <form id="project-report-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" data-object-list-url="<?php echo $this->createUrl('report/objectlist'); ?>">
        <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

        <fieldset>
            <div class="control-group" id="template-list">
                <label class="control-label" for="RiskMatrixForm_templateId"><?php echo Yii::t('app', 'Template'); ?></label>
                <div class="controls">
                    <select class="input-xlarge" id="RiskMatrixForm_templateId" name="RiskMatrixForm[templateId]" onchange="user.report.riskMatrixFormChange(this);">
                        <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                        <?php foreach ($templates as $template): ?>
                            <option value="<?php echo $template->id; ?>"><?php echo CHtml::encode($template->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="help-block hide"><?php echo Yii::t('app', 'This template has no categories.'); ?></p>
                </div>
            </div>

            <div class="control-group hide" id="client-list">
                <label class="control-label" for="RiskMatrixForm_clientId"><?php echo Yii::t('app', 'Client'); ?></label>
                <div class="controls">
                    <select class="input-xlarge" id="RiskMatrixForm_clientId" name="RiskMatrixForm[clientId]" onchange="user.report.riskMatrixFormChange(this);">
                        <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client->id; ?>"><?php echo CHtml::encode($client->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="help-block hide"><?php echo Yii::t('app', 'This client has no projects.'); ?></p>
                </div>
            </div>

            <div class="hide control-group" id="project-list">
                <label class="control-label" for="RiskMatrixForm_projectId"><?php echo Yii::t('app', 'Project'); ?></label>
                <div class="controls">
                    <select class="input-xlarge" id="RiskMatrixForm_projectId" name="RiskMatrixForm[projectId]" onchange="user.report.riskMatrixFormChange(this);">
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
<?php endif; ?>
