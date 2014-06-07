<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group">
            <label class="control-label"><?php echo Yii::t("app", "User"); ?></label>
            <div class="controls form-text">
                <?php echo CHtml::encode($user->user->name ? $user->user->name : $user->user->email); ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo Yii::t("app", "Spent So Far"); ?></label>
            <div class="controls form-text">
                <?php echo sprintf("%.1f", $user->hours_spent); ?> /
                <?php echo sprintf("%.1f", $user->hours_allocated); ?>
            </div>
        </div>

        <div class="control-group <?php if ($form->getError("hoursSpent")) echo "error"; ?>">
            <label class="control-label" for="ProjectTrackTimeForm_hoursSpent"><?php echo Yii::t("app", "Hours Spent"); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ProjectTrackTimeForm_hoursSpent" name="ProjectTrackTimeForm[hoursSpent]" value="<?php echo CHtml::encode($form->hoursSpent); ?>">
                <?php if ($form->getError("hoursSpent")): ?>
                    <p class="help-block"><?php echo $form->getError("hoursSpent"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Save"); ?></button>
        </div>
    </fieldset>
</form>
