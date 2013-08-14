<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group">
            <label class="control-label"><?php echo Yii::t("app", "Name"); ?></label>
            <div class="controls form-text">
                <?php echo CHtml::encode($check->localizedName); ?>
            </div>
        </div>

        <?php if ($check->localizedBackgroundInfo): ?>
            <div class="control-group">
                <label class="control-label"><?php echo Yii::t("app", "Background Info"); ?></label>
                <div class="controls form-text">
                    <?php echo CHtml::encode($check->localizedBackgroundInfo); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($check->localizedHints): ?>
            <div class="control-group">
                <label class="control-label"><?php echo Yii::t("app", "Hints"); ?></label>
                <div class="controls form-text">
                    <?php echo CHtml::encode($check->localizedHints); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($check->localizedQuestion): ?>
            <div class="control-group">
                <label class="control-label"><?php echo Yii::t("app", "Question"); ?></label>
                <div class="controls form-text">
                    <?php echo CHtml::encode($check->localizedQuestion); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="control-group <?php if ($model->getError("controlId")) echo "error"; ?>">
            <label class="control-label" for="IncomingCheckEditForm_controlId"><?php echo Yii::t("app", "Control"); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="IncomingCheckEditForm_controlId" name="IncomingCheckEditForm[controlId]">
                    <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>
                    <?php foreach ($categories as $cat): ?>
                        <?php foreach ($cat->controls as $ctrl): ?>
                            <option value="<?php echo $ctrl->id; ?>">
                                <?php echo CHtml::encode($cat->localizedName); ?> / <?php echo CHtml::encode($ctrl->localizedName); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
                <?php if ($model->getError("controlId")): ?>
                    <p class="help-block"><?php echo $model->getError("controlId"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Save"); ?></button>
        </div>
    </fieldset>
</form>
