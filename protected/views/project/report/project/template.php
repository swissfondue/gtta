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
        <div class="control-group <?php if ($form->getError("templateId")) echo "error"; ?>">
            <label class="control-label" for="ProjectReportTemplateForm_templateId"><?php echo Yii::t("app", "Template"); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="ProjectReportTemplateForm_templateId" name="ProjectReportTemplateForm[templateId]">
                    <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>
                    <?php foreach ($templates as $t): ?>
                        <option
                            value="<?php echo $t->id; ?>"
                            <?php if ($t->id == $project->report_template_id) echo "selected"; ?>>
                            <?php echo CHtml::encode($t->localizedName); ?> (<?php echo $typeNames[$t->type]; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($form->getError("templateId")): ?>
                    <p class="help-block"><?php echo $form->getError("templateId"); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Next"); ?></button>
        </div>
    </fieldset>
</form>