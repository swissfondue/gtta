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
                <?= CHtml::encode($template->name); ?> (RTF)

                <a href="<?= $this->createUrl("projectReport/template", ["id" => $project->id]); ?>"><i class="icon icon-edit"></i></a>
            </div>
        </div>

        <div class="control-group" id="target-list">
            <label class="control-label"><?php echo Yii::t("app", "Targets"); ?></label>
            <div class="controls">
                <ul class="report-target-list">
                    <?php foreach ($project->targets as $target): ?>
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
        </div>

        <div class="control-group">
            <div class="controls form-text">
                <a href="#show-advanced" onclick="system.toggleBlock('#advanced-options');"><?= Yii::t("app", "Advanced Options"); ?></a>
            </div>
        </div>

        <div class="<?php if (!$form->hasErrors()) echo "hide"; ?>" id="advanced-options">
            <div class="control-group <?php if ($form->getError("fontSize")) echo "error"; ?>">
                <label class="control-label" for="ProjectReportForm_fontSize"><?php echo Yii::t("app", "Font Size"); ?></label>
                <div class="controls">
                    <input type="text" class="input-xlarge" id="ProjectReportForm_fontSize" name="ProjectReportForm[fontSize]" value="<?php echo $form->fontSize ? CHtml::encode($form->fontSize) : Yii::app()->params["reports"]["fontSize"]; ?>">
                    <?php if ($form->getError("fontSize")): ?>
                        <p class="help-block"><?php echo $form->getError("fontSize"); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group <?php if ($form->getError("fontFamily")) echo "error"; ?>">
                <label class="control-label" for="ProjectReportForm_fontFamily"><?php echo Yii::t("app", "Font Family"); ?></label>
                <div class="controls">
                    <select class="input-xlarge" id="ProjectReportForm_fontFamily" name="ProjectReportForm[fontFamily]">
                        <?php foreach (Yii::app()->params["reports"]["fonts"] as $font): ?>
                            <option value="<?php echo $font; ?>"<?php if ($font == Yii::app()->params["reports"]["font"]) echo "selected"; ?>><?php echo $font; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($form->getError("fontFamily")): ?>
                        <p class="help-block"><?php echo $form->getError("fontFamily"); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group <?php if ($form->getError("pageMargin")) echo "error"; ?>">
                <label class="control-label" for="ProjectReportForm_pageMargin"><?php echo Yii::t("app", "Page Margin"); ?></label>
                <div class="controls">
                    <input type="text" class="input-xlarge" id="ProjectReportForm_pageMargin" name="ProjectReportForm[pageMargin]" value="<?php echo $form->pageMargin ? CHtml::encode($form->pageMargin) : Yii::app()->params["reports"]["pageMargin"]; ?>">
                    <?php if ($form->getError("pageMargin")): ?>
                        <p class="help-block"><?php echo $form->getError("pageMargin"); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="control-group <?php if ($form->getError("cellPadding")) echo "error"; ?>">
                <label class="control-label" for="ProjectReportForm_cellPadding"><?php echo Yii::t("app", "Cell Padding"); ?></label>
                <div class="controls">
                    <input type="text" class="input-xlarge" id="ProjectReportForm_cellPadding" name="ProjectReportForm[cellPadding]" value="<?php echo $form->cellPadding ? CHtml::encode($form->cellPadding) : Yii::app()->params["reports"]["cellPadding"]; ?>">
                    <?php if ($form->getError("cellPadding")): ?>
                        <p class="help-block"><?php echo $form->getError("cellPadding"); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($template->hasSection(ReportSection::TYPE_RISK_MATRIX)): ?>
                <div class="control-group <?php if ($form->getError("riskTemplateId")) echo "error"; ?>" id="risk-template-list">
                    <label class="control-label" for="ProjectReportForm_riskTemplateId"><?php echo Yii::t("app", "Risk Matrix Template"); ?></label>
                    <div class="controls">
                        <select class="input-xlarge" id="ProjectReportForm_riskTemplateId" name="ProjectReportForm[riskTemplateId]" onchange="system.report.projectFormChange(this);">
                            <option value="0"><?php echo Yii::t("app", "Please select..."); ?></option>
                            <?php foreach ($riskTemplates as $t): ?>
                                <option value="<?php echo $t->id; ?>"><?php echo CHtml::encode($t->localizedName); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($form->getError("riskTemplateId")): ?>
                            <p class="help-block"><?php echo $form->getError("riskTemplateId"); ?></p>
                        <?php endif; ?>
                        <p class="help-block hide"><?php echo Yii::t("app", "This template has no categories."); ?></p>
                    </div>
                </div>

                <div id="check-list" class="hide">
                    <hr>
                    <div class="container">
                        <div class="row">
                            <div class="span8">
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="control-group">
                <label class="control-label"><?php echo Yii::t("app", "Title Page"); ?></label>
                <div class="controls">
                    <input type="checkbox" id="ProjectReportForm_title" name="ProjectReportForm[title]" value="1" checked>
                </div>
            </div>

            <?php if ($template->hasSection(ReportSection::TYPE_VULNERABILITIES) || $template->hasSection(ReportSection::TYPE_INFO_CHECKS)): ?>
                <div class="control-group" id="checks-fields">
                    <label class="control-label" for="ProjectReportForm_fields"><?php echo Yii::t("app", "Vulnerability List Check Fields"); ?></label>
                    <div class="controls">
                        <?php foreach ($fields as $field): ?>
                            <label class="checkbox">
                                <input type="checkbox" id="ProjectReportForm_fields" name="ProjectReportForm[fields][]" value="<?= $field->name ?>" checked="checked">
                                <?= $field->localizedTitle ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($template->hasSection(ReportSection::TYPE_VULNERABILITIES)): ?>
                <div class="control-group" id="info-checks-location-list">
                    <label class="control-label" for="ProjectReportForm_infoChecksLocation"><?php echo Yii::t("app", "Info Checks Location"); ?></label>
                    <div class="controls">
                        <select class="input-xlarge" id="ProjectReportForm_infoChecksLocation" name="ProjectReportForm[infoChecksLocation]" onchange="system.report.projectFormChange(this);">
                            <?php foreach ($infoChecksLocation as $id => $loc): ?>
                                <option value="<?php echo $id; ?>"><?php echo $loc; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

            <div class="control-group" id="file-type">
                <label class="control-label" for="ProjectReportForm_fileType"><?php echo Yii::t("app", "Download File Type"); ?></label>
                <div class="controls">
                    <label class="radio">
                        <input type="radio" name="ProjectReportForm[fileType]" value="<?php echo ProjectReportForm::FILE_TYPE_RTF; ?>" checked="checked" >
                        <?php echo Yii::t("app", "RTF"); ?>
                    </label>
                    <label class="radio">
                        <input type="radio" name="ProjectReportForm[fileType]" value="<?php echo ProjectReportForm::FILE_TYPE_ZIP; ?>" >
                        <?php echo Yii::t("app", "RTF + Attachments"); ?>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <?php echo Yii::t("app", "Generate"); ?>
            </button>
        </div>
    </fieldset>
</form>