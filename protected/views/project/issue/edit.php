<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?= $this->createUrl("project/issue", ["id" => $project->id, $issue->id]); ?>"><?= Yii::t("app", "View"); ?></a></li>
            <li class="active"><a href="<?= $this->createUrl("project/editissue", ["id" => $project->id, $issue->id]); ?>"><?= Yii::t("app", "Edit"); ?></a></li>
        </ul>
    </div>

    <h1><?= CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?= Yii::app()->request->url; ?>" method="post">
    <fieldset>
        <div class="control-group">
            <label class="control-label"><?= Yii::t("app", "Type"); ?></label>
            <div class="controls form-text"><?= "TYPE"; ?></div>
        </div>

        <div class="control-group">
            <label class="control-label"><?= Yii::t("app", "Version"); ?></label>
            <div class="controls form-text"><?= "VERSION"; ?></div>
        </div>

        <div class="control-group">
            <label class="control-label"><?= Yii::t("app", "Dependencies"); ?></label>
            <div class="controls form-text"></div>
        </div>
    </fieldset>
</form>