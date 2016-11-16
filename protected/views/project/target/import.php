<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form id="TargetImportForm" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError("type")) echo "error"; ?>">
            <label class="control-label" for="TargetImportForm_type"><?php echo Yii::t("app", "Type"); ?></label>
            <div class="controls">
                <select id="TargetImportForm_type" name="TargetImportForm[type]" onchange="admin.project.importTypeChanged($(this).val())">
                    <option value=""><?= Yii::t("app", "Please select..."); ?></option>
                    <?php foreach ($types as $type => $typeData): ?>
                        <option value="<?= $type; ?>" <?php if ($type == $model->type) echo "selected"; ?>><?= $typeData["name"]; ?></option>
                    <?php endforeach;?>
                </select>
                <?php if ($model->getError("type")): ?>
                    <p class="help-block"><?php echo $model->getError("type"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group nessus-import-input <?php if ($model->type != ImportManager::TYPE_NESSUS) print "hide"; ?> <?php if ($model->getError("mappingId")) echo "error"; ?>">
            <label class="control-label" for="TargetImportForm_mappingId"><?php echo Yii::t("app", "Mapping"); ?></label>
            <div class="controls">
                <select id="TargetImportForm_mappingId" name="TargetImportForm[mappingId]">
                    <option value="0"><?= Yii::t("app", "New mapping..."); ?></option>
                    <?php foreach ($mappings as $mapping): ?>
                        <option value="<?= $mapping->id; ?>" <?php if ($mapping->id == $model->mappingId) echo "selected"; ?>><?= $mapping->name; ?></option>
                    <?php endforeach;?>
                </select>
                <?php if ($model->getError("mappingId")): ?>
                    <p class="help-block"><?php echo $model->getError("type"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError("file")) echo "error"; ?>">
            <label class="control-label" for="TargetImportForm_file"><?php echo Yii::t("app", "File"); ?></label>
            <div class="controls">
                <input type="file" id="TargetImportForm_file" name="TargetImportForm[file]">
                <?php if ($model->getError("file")): ?>
                    <p class="help-block"><?php echo $model->getError("file"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button id="restore" type="submit" class="btn"><?php echo Yii::t("app", "Import"); ?></button>
        </div>
    </fieldset>
</form>