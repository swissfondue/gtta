<div class="active-header">
    <?php if (!$check->isNewRecord): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li class="active"><a href="<?php echo $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <?php if ($check->automated): ?>
                    <li><a href="<?php echo $this->createUrl('check/inputs', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Inputs'); ?></a></li>
                <?php endif; ?>
                <li><a href="<?php echo $this->createUrl('check/results', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Results'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('check/solutions', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Solutions'); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <ul class="nav nav-tabs" id="languages-tab">
            <li class="active"><a href="#default"><?php echo Yii::t('app', 'Default'); ?></a></li>
            <?php foreach ($languages as $language): ?>
                <li>
                    <a href="#<?php echo CHtml::encode($language->code); ?>">
                        <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png" alt="<?php echo CHtml::encode($language->name); ?>">
                        <?php echo CHtml::encode($language->name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="default">
                <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
                    <label class="control-label" for="CheckEditForm_name"><?php echo Yii::t('app', 'Name'); ?></label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" id="CheckEditForm_name" name="CheckEditForm[name]" value="<?php echo CHtml::encode($model->name); ?>" onkeyup="admin.check.updateTiedField('CheckEditForm_name', 'CheckEditForm_localizedItems_<?php echo $defaultLanguage; ?>_name');">
                        <?php if ($model->getError('name')): ?>
                            <p class="help-block"><?php echo $model->getError('name'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="CheckEditForm_backgroundInfo"><?php echo Yii::t('app', 'Background Info'); ?></label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" id="CheckEditForm_backgroundInfo" name="CheckEditForm[backgroundInfo]" onkeyup="admin.check.updateTiedField('CheckEditForm_backgroundInfo', 'CheckEditForm_localizedItems_<?php echo $defaultLanguage; ?>_backgroundInfo');" value="<?php echo CHtml::encode($model->backgroundInfo); ?>">
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="CheckEditForm_hints"><?php echo Yii::t('app', 'Hints'); ?></label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" id="CheckEditForm_hints" name="CheckEditForm[hints]" onkeyup="admin.check.updateTiedField('CheckEditForm_hints', 'CheckEditForm_localizedItems_<?php echo $defaultLanguage; ?>_hints');" value="<?php echo CHtml::encode($model->hints); ?>">
                    </div>
                </div>
                
                <div class="control-group">
                    <label class="control-label" for="CheckEditForm_question"><?php echo Yii::t('app', 'Question'); ?></label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" id="CheckEditForm_question" name="CheckEditForm[question]" onkeyup="admin.check.updateTiedField('CheckEditForm_question', 'CheckEditForm_localizedItems_<?php echo $defaultLanguage; ?>_question');" value="<?php echo CHtml::encode($model->question); ?>">
                    </div>
                </div>
            </div>
            <?php foreach ($languages as $language): ?>
                <div class="tab-pane" id="<?php echo CHtml::encode($language->code); ?>">
                    <div class="control-group">
                        <label class="control-label" for="CheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name"><?php echo Yii::t('app', 'Name'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name" name="CheckEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][name]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['name']) : ''; ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="CheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_backgroundInfo"><?php echo Yii::t('app', 'Background Info'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_backgroundInfo" name="CheckEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][backgroundInfo]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['backgroundInfo']) : ''; ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="CheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_hints"><?php echo Yii::t('app', 'Hints'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_hints" name="CheckEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][hints]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['hints']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label class="control-label" for="CheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_reference"><?php echo Yii::t('app', 'Reference'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_reference" name="CheckEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][reference]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['reference']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label class="control-label" for="CheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_question"><?php echo Yii::t('app', 'Question'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_question" name="CheckEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][question]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['question']) : ''; ?>">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div>
            <hr>
        </div>

        <div class="control-group <?php if ($model->getError('controlId')) echo 'error'; ?>">
            <label class="control-label" for="CheckEditForm_controlId"><?php echo Yii::t('app', 'Control'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="CheckEditForm_controlId" name="CheckEditForm[controlId]">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($controls as $ctrl): ?>
                        <option value="<?php echo $ctrl->id; ?>" <?php if ($ctrl->id == $model->controlId) echo 'selected'; ?>><?php echo CHtml::encode($ctrl->localizedName); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('controlId')): ?>
                    <p class="help-block"><?php echo $model->getError('controlId'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('referenceId')) echo 'error'; ?>">
            <label class="control-label" for="CheckEditForm_referenceId"><?php echo Yii::t('app', 'Reference'); ?></label>
            <div class="controls">
                <select class="input-medium" id="CheckEditForm_referenceId" name="CheckEditForm[referenceId]">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($references as $reference): ?>
                        <option value="<?php echo $reference->id; ?>" <?php if ($reference->id == $model->referenceId) echo 'selected'; ?>><?php echo CHtml::encode($reference->name); ?></option>
                    <?php endforeach; ?>
                </select>

                &nbsp;-&nbsp;

                <input type="text" class="input-small" id="CheckEditForm_referenceCode" name="CheckEditForm[referenceCode]" value="<?php echo CHtml::encode($model->referenceCode); ?>">

                <?php if ($model->getError('referenceId')): ?>
                    <p class="help-block"><?php echo $model->getError('referenceId'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('referenceUrl')) echo 'error'; ?>">
            <label class="control-label" for="CheckEditForm_referenceUrl"><?php echo Yii::t('app', 'Reference URL'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="CheckEditForm_referenceUrl" name="CheckEditForm[referenceUrl]" value="<?php echo CHtml::encode($model->referenceUrl); ?>">
                <?php if ($model->getError('referenceUrl')): ?>
                    <p class="help-block"><?php echo $model->getError('referenceUrl'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <hr>
        </div>
        
        <div class="control-group <?php if ($model->getError('protocol')) echo 'error'; ?>">
            <label class="control-label" for="CheckEditForm_protocol"><?php echo Yii::t('app', 'Protocol'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="CheckEditForm_protocol" name="CheckEditForm[protocol]" value="<?php echo CHtml::encode($model->protocol); ?>">
                <?php if ($model->getError('protocol')): ?>
                    <p class="help-block"><?php echo $model->getError('protocol'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('port')) echo 'error'; ?>">
            <label class="control-label" for="CheckEditForm_port"><?php echo Yii::t('app', 'Port'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="CheckEditForm_port" name="CheckEditForm[port]" value="<?php echo $model->port; ?>">
                <?php if ($model->getError('port')): ?>
                    <p class="help-block"><?php echo $model->getError('port'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="CheckEditForm_automated"><?php echo Yii::t('app', 'Automated'); ?></label>
            <div class="controls">
                <input type="checkbox" id="CheckEditForm_automated" name="CheckEditForm[automated]" value="1" <?php if ($model->automated) echo 'checked="checked"'; ?> onchange="admin.check.toggleScriptField();">
            </div>
        </div>

        <div class="control-group <?php if (!$model->automated) echo 'hidden-object'; ?> <?php if ($model->getError('script')) echo 'error'; ?>" id="script-input">
            <label class="control-label" for="CheckEditForm_script"><?php echo Yii::t('app', 'Script'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="CheckEditForm_script" name="CheckEditForm[script]" value="<?php echo CHtml::encode($model->script); ?>">
                <?php if ($model->getError('script')): ?>
                    <p class="help-block"><?php echo $model->getError('script'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="CheckEditForm_advanced"><?php echo Yii::t('app', 'Advanced'); ?></label>
            <div class="controls">
                <input type="checkbox" id="CheckEditForm_advanced" name="CheckEditForm[advanced]" value="1" <?php if ($model->advanced) echo 'checked="checked"'; ?>>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="CheckEditForm_multipleSolutions"><?php echo Yii::t('app', 'Multiple Solutions'); ?></label>
            <div class="controls">
                <input type="checkbox" id="CheckEditForm_multipleSolutions" name="CheckEditForm[multipleSolutions]" value="1" <?php if ($model->multipleSolutions) echo 'checked="checked"'; ?>>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('effort')) echo 'error'; ?>">
            <label class="control-label" for="CheckEditForm_effort"><?php echo Yii::t('app', 'Effort'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="CheckEditForm_effort" name="CheckEditForm[effort]">
                    <?php foreach ($efforts as $effort): ?>
                        <option value="<?php echo $effort; ?>" <?php if ($effort == $model->effort) echo 'selected'; ?>><?php echo $effort; ?> <?php echo Yii::t('app', 'minutes'); ?></option>
                    <?php endforeach; ?>
                </select>

                <?php if ($model->getError('effort')): ?>
                    <p class="help-block"><?php echo $model->getError('effort'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>

<script>
    $('#languages-tab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })
</script>