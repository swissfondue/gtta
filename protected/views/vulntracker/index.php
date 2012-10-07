<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form id="object-selection-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" data-object-list-url="<?php echo $this->createUrl('app/objectlist'); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group" id="client-list">
            <label class="control-label" for="ProjectSelectForm_clientId"><?php echo Yii::t('app', 'Client'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectSelectForm_clientId" name="ProjectSelectForm[clientId]" onchange="system.vuln.projectSelectFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client->id; ?>"><?php echo CHtml::encode($client->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="help-block hide"><?php echo Yii::t('app', 'This client has no projects.'); ?></p>
            </div>
        </div>

        <div class="hide control-group" id="project-list">
            <label class="control-label" for="ProjectSelectForm_projectId"><?php echo Yii::t('app', 'Project'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectSelectForm_projectId" name="ProjectSelectForm[projectId]" onchange="system.vuln.projectSelectFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                </select>
                <p class="help-block hide"><?php echo Yii::t('app', 'This project has no targets.'); ?></p>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn" disabled><?php echo Yii::t('app', 'View'); ?></button>
        </div>
    </fieldset>
</form>