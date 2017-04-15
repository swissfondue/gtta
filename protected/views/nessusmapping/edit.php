<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?= $this->createUrl("nessusmapping/edit", ["id" => $mapping->id]); ?>"><?= Yii::t("app", "Edit"); ?></a></li>
            <li><a href="<?= $this->createUrl("nessusmapping/view", ["id" => $mapping->id]); ?>"><?= Yii::t("app", "Mapping"); ?></a></li>
        </ul>
    </div>

    <h1><?= CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?= Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?= Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <ul class="nav nav-tabs" id="languages-tab">
            <?php foreach ($languages as $language): ?>
                <li<?php if ($language->default) echo ' class="active"'; ?>>
                    <a href="#<?= CHtml::encode($language->code); ?>">
                        <img src="<?= Yii::app()->baseUrl; ?>/images/languages/<?= CHtml::encode($language->code); ?>.png" alt="<?= CHtml::encode($language->name); ?>">
                        <?= CHtml::encode($language->name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <?php foreach ($languages as $language): ?>
                <div class="tab-pane <?php if ($language->default) echo "active"; ?>" id="<?= CHtml::encode($language->code); ?>">
                    <div class="control-group <?php if ($form->getError("name")) echo "error"; ?>">
                        <label class="control-label" for="NessusMappingEditForm_localizedItems_<?= CHtml::encode($language->id); ?>_name"><?= Yii::t('app', 'Name'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="NessusMappingEditForm_localizedItems_<?= CHtml::encode($language->id); ?>_name" name="NessusMappingEditForm[localizedItems][<?= CHtml::encode($language->id); ?>][name]" value="<?= isset($form->localizedItems[$language->id]) ? CHtml::encode($form->localizedItems[$language->id]["name"]) : ""; ?>">
                            <?php if ($form->getError("name")): ?>
                                <p class="help-block"><?= $form->getError("name"); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?= Yii::t("app", "Save"); ?></button>
        </div>
    </fieldset>
</form>

<script>
    $('#languages-tab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })
</script>