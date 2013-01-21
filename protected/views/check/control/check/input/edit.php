<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <ul class="nav nav-tabs" id="languages-tab">
            <?php foreach ($languages as $language): ?>
                <li<?php if ($language->default) echo ' class="active"'; ?>>
                    <a href="#<?php echo CHtml::encode($language->code); ?>">
                        <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png" alt="<?php echo CHtml::encode($language->name); ?>">
                        <?php echo CHtml::encode($language->name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <?php foreach ($languages as $language): ?>
                <div class="tab-pane<?php if ($language->default) echo ' active'; ?>" id="<?php echo CHtml::encode($language->code); ?>">
                    <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
                        <label class="control-label" for="CheckInputEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name"><?php echo Yii::t('app', 'Name'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckInputEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name" name="CheckInputEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][name]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['name']) : ''; ?>">
                            <?php if ($model->getError('name')): ?>
                                <p class="help-block"><?php echo $model->getError('name'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="CheckInputEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_description"><?php echo Yii::t('app', 'Description'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="CheckInputEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_description" name="CheckInputEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][description]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['description']) : ''; ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="CheckInputEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_value"><?php echo Yii::t('app', 'Value'); ?></label>
                        <div class="controls">
                            <textarea class="wysiwyg" id="CheckInputEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_value" name="CheckInputEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][value]"><?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['value']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div>
            <hr>
        </div>
        
        <div class="control-group <?php if ($model->getError('type')) echo 'error'; ?>">
            <label class="control-label" for="CheckInputEditForm_type"><?php echo Yii::t('app', 'Type'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="CheckInputEditForm_type" name="CheckInputEditForm[type]">
                    <?php foreach ($types as $k => $v): ?>
                        <option value="<?php echo $k; ?>" <?php if ($k == $model->type) echo 'selected'; ?>><?php echo CHtml::encode($v); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError('type')): ?>
                    <p class="help-block"><?php echo $model->getError('type'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('sortOrder')) echo 'error'; ?>">
            <label class="control-label" for="CheckInputEditForm_sortOrder"><?php echo Yii::t('app', 'Sort Order'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="CheckInputEditForm_sortOrder" name="CheckInputEditForm[sortOrder]" value="<?php echo $model->sortOrder ? $model->sortOrder : 0; ?>">
                <?php if ($model->getError('sortOrder')): ?>
                    <p class="help-block"><?php echo $model->getError('sortOrder'); ?></p>
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