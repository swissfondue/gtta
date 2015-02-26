<div class="active-header">
    <?php if (!$template->isNewRecord): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li><a href="<?php echo $this->createUrl("checklisttemplate/viewtemplate", array("id" => $category->id, "template" => $template->id)); ?>"><?php echo Yii::t("app", "Categories"); ?></a></li>
                <li class="active"><a href="<?php echo $this->createUrl("checklisttemplate/edittemplate", array("id" => $category->id, "template" => $template->id)); ?>"><?php echo Yii::t("app", "Edit"); ?></a></li>
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
                        <label class="control-label" for="ChecklistTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name"><?php echo Yii::t('app', 'Name'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="ChecklistTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name" name="ChecklistTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][name]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['name']) : ''; ?>">
                            <?php if ($model->getError('name')): ?>
                                <p class="help-block"><?php echo $model->getError('name'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="control-group <?php if ($model->getError('description')) echo 'error'; ?>">
                        <label class="control-label" for="ChecklistTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_description"><?php echo Yii::t('app', 'Description'); ?></label>
                        <div class="controls">
                            <textarea class="max-width" rows="10" id="ChecklistTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_description" name="ChecklistTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][description]"><?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['description']) : ''; ?></textarea>
                            <?php if ($model->getError('description')): ?>
                                <p class="help-block"><?php echo $model->getError('description'); ?></p>
                            <?php endif; ?>
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