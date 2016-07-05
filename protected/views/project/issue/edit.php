<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?= $this->createUrl("project/viewissue", ["id" => $project->id, "issue" => $issue->id]); ?>"><?= Yii::t("app", "View"); ?></a></li>
            <li class="active"><a href="<?= $this->createUrl("project/editissue", ["id" => $project->id, $issue->id]); ?>"><?= Yii::t("app", "Edit"); ?></a></li>
        </ul>
    </div>

    <h1><?= CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?= $issue->id ?>" name="IssueEditForm[id]">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($form->getError("name")) echo "error"; ?>">
            <label class="control-label" for="IssueEditForm_name"><?php echo Yii::t("app", "Name"); ?></label>
            <div class="controls">
                <input type="text"
                       class="input-xlarge"
                       id="IssueEditForm_name"
                       name="IssueEditForm[name]"
                       value="<?php echo CHtml::encode($issue->name); ?>">
                <?php if ($form->getError("name")): ?>
                    <p class="help-block"><?php echo $form->getError("name"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Save"); ?></button>
        </div>
    </fieldset>
</form>