<div class="active-header">
    <div class="pull-right">
        <?= $this->renderPartial("//project/partial/submenu", ["page" => "project-report", "project" => $project]); ?>
    </div>

    <h1><?= CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <?php $typeNames = ReportTemplate::getValidTypeNames(); ?>

    <fieldset>
        <div class="control-group <?php if ($form->getError("reportTemplateId")) echo "error"; ?>">
            <label class="control-label" for="ProjectReportTemplateForm_reportTemplateId">
                <?php echo Yii::t("app", "Template"); ?>
            </label>
            <div class="controls">
                <select
                    class="input-xlarge"
                    id="ProjectReportTemplateForm_reportTemplateId"
                    name="ProjectReportTemplateForm[reportTemplateId]"
                    onchange="system.report.projectTemplateFormChange(this);">
                    <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>
                    <?php foreach ($templates as $t): ?>
                        <option
                            value="<?php echo $t->id; ?>"
                            data-type="<?= $t->type; ?>"
                            <?php if ($t->id == $project->report_template_id) echo "selected"; ?>>
                            <?php echo CHtml::encode($t->localizedName); ?> (<?php echo $typeNames[$t->type]; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($form->getError("reportTemplateId")): ?>
                    <p class="help-block"><?php echo $form->getError("reportTemplateId"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group custom-report <?php if ($template && $template->type != ReportTemplate::TYPE_RTF) echo "hide"; ?>">
            <label class="control-label" for="ProjectReportTemplateForm_customReport">
                <?php echo Yii::t("app", "Custom Report"); ?>
            </label>
            <div class="controls">
                <input type="checkbox" id="ProjectReportTemplateForm_customReport" name="ProjectReportTemplateForm[customReport]" value="1" <?php if ($form->customReport) echo "checked"; ?>>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Next"); ?></button>
        </div>
    </fieldset>
</form>