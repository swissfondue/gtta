<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

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
                    <label class="control-label" for="RiskCategoryEditForm_name"><?php echo Yii::t('app', 'Name'); ?></label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" id="RiskCategoryEditForm_name" name="RiskCategoryEditForm[name]" value="<?php echo CHtml::encode($model->name); ?>" onkeyup="admin.check.updateTiedField('RiskCategoryEditForm_name', 'RiskCategoryEditForm_localizedItems_<?php echo $defaultLanguage; ?>_name');">
                        <?php if ($model->getError('name')): ?>
                            <p class="help-block"><?php echo $model->getError('name'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php foreach ($languages as $language): ?>
                <div class="tab-pane" id="<?php echo CHtml::encode($language->code); ?>">
                    <div class="control-group">
                        <label class="control-label" for="RiskCategoryEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name"><?php echo Yii::t('app', 'Name'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="RiskCategoryEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name" name="RiskCategoryEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][name]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['name']) : ''; ?>">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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