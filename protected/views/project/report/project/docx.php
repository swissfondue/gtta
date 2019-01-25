<div class="active-header">
    <div class="pull-right">
        <?= $this->renderPartial("//project/partial/submenu", ["page" => "project-report", "project" => $project]); ?>
    </div>

    <h1><?= CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form id="object-selection-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" data-object-list-url="<?php echo $this->createUrl("app/objectlist"); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <?php $typeNames = ReportTemplate::getValidTypeNames(); ?>

    <fieldset>
        <div class="control-group">
            <label class="control-label">
                <?php echo Yii::t("app", "Template"); ?>
            </label>

            <div class="controls form-text">
                <?= CHtml::encode($template->name); ?> (DOCX)

                <a href="<?= $this->createUrl("projectReport/template", ["id" => $project->id]); ?>"><i class="icon icon-edit"></i></a>
            </div>
        </div>

        <div class="control-group" id="target-list">
            <label class="control-label">
                <?php echo Yii::t("app", "Targets"); ?>
            </label>

            <div class="controls">
                <ul class="report-target-list">
                    <?php foreach ($targets as $target): ?>
                        <li>
                            <label>
                                <input
                                    checked
                                    type="checkbox"
                                    id="ProjectReportForm_targetIds_<?= $target->id; ?>"
                                    name="ProjectReportForm[targetIds][]"
                                    value="<?= $target->id; ?>"
                                    onclick="system.report.projectFormChange(this);">

                                <?= $target->host; ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="control-group" id="target-list">
                <label class="control-label">
                    <?php echo Yii::t("app", "Delete titles of control blocks"); ?>
                </label>
                <div class="controls">
                    <input
                            checked
                            type="checkbox"
                            id="ProjectReportForm_deleteTitles"
                            name="ProjectReportForm[deleteTitles]"
                            value="1"
                            onclick="system.report.projectFormChange(this);">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Generate"); ?></button>
        </div>
    </fieldset>
</form>