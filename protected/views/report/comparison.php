<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form id="object-selection-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" data-object-list-url="<?php echo $this->createUrl('app/objectlist'); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('fontSize')) echo 'error'; ?>">
            <label class="control-label" for="ProjectComparisonForm_fontSize"><?php echo Yii::t('app', 'Font Size'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ProjectComparisonForm_fontSize" name="ProjectComparisonForm[fontSize]" value="<?php echo $model->fontSize ? CHtml::encode($model->fontSize) : Yii::app()->params['reports']['fontSize']; ?>">
                <?php if ($model->getError('fontSize')): ?>
                    <p class="help-block"><?php echo $model->getError('fontSize'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('fontFamily')) echo 'error'; ?>">
            <label class="control-label" for="ProjectComparisonForm_fontFamily"><?php echo Yii::t('app', 'Font Family'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectComparisonForm_fontFamily" name="ProjectComparisonForm[fontFamily]">
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
            <label class="control-label" for="ProjectComparisonForm_pageMargin"><?php echo Yii::t('app', 'Page Margin'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ProjectComparisonForm_pageMargin" name="ProjectComparisonForm[pageMargin]" value="<?php echo $model->pageMargin ? CHtml::encode($model->pageMargin) : Yii::app()->params['reports']['pageMargin']; ?>">
                <?php if ($model->getError('pageMargin')): ?>
                    <p class="help-block"><?php echo $model->getError('pageMargin'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('cellPadding')) echo 'error'; ?>">
            <label class="control-label" for="ProjectComparisonForm_cellPadding"><?php echo Yii::t('app', 'Cell Padding'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ProjectComparisonForm_cellPadding" name="ProjectComparisonForm[cellPadding]" value="<?php echo $model->cellPadding ? CHtml::encode($model->cellPadding) : Yii::app()->params['reports']['cellPadding']; ?>">
                <?php if ($model->getError('cellPadding')): ?>
                    <p class="help-block"><?php echo $model->getError('cellPadding'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group" id="client-list">
            <label class="control-label" for="ProjectComparisonForm_clientId"><?php echo Yii::t('app', 'Client'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectComparisonForm_clientId" name="ProjectComparisonForm[clientId]" onchange="system.report.comparisonFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client->id; ?>"><?php echo CHtml::encode($client->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="help-block hide"><?php echo Yii::t('app', 'This client has no projects.'); ?></p>
            </div>
        </div>

        <div class="hide control-group" id="project-list-1">
            <label class="control-label" for="ProjectComparisonForm_projectId1"><?php echo Yii::t('app', 'First Project'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectComparisonForm_projectId1" name="ProjectComparisonForm[projectId1]" onchange="system.report.comparisonFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                </select>
            </div>
        </div>

        <div class="hide control-group" id="project-list-2">
            <label class="control-label" for="ProjectComparisonForm_projectId2"><?php echo Yii::t('app', 'Second Project'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectComparisonForm_projectId2" name="ProjectComparisonForm[projectId2]" onchange="system.report.comparisonFormChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn" disabled><?php echo Yii::t('app', 'Generate'); ?></button>
        </div>
    </fieldset>
</form>