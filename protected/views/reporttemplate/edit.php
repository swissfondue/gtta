<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.ui.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.iframe-transport.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.fileupload.js"></script>

<div class="active-header">
    <?php if (!$template->isNewRecord && $template->type == ReportTemplate::TYPE_RTF): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li class="active"><a href="<?php echo $this->createUrl('reporttemplate/edit', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('reporttemplate/summary', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Summary Blocks'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('reporttemplate/sections', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Vulnerability Sections'); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="container">
            <div class="row">
                <div class="span8">
                    <div class="control-group <?php if ($model->getError("type")) echo "error"; ?>">
                        <label class="control-label" for="ReportTemplateEditForm_type"><?php echo Yii::t("app", "Type"); ?></label>
                        <div class="controls">
                            <select class="input-xlarge" id="ReportTemplateEditForm_type" name="ReportTemplateEditForm[type]" onchange="admin.reportTemplate.onTypeChange();">
                                <?php foreach (ReportTemplate::getValidTypeNames() as $type => $name): ?>
                                    <option value="<?php echo $type; ?>" <?php if ($type == $model->type) echo "selected"; ?>><?php echo $name; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($model->getError("type")): ?>
                                <p class="help-block"><?php echo $model->getError("type"); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <ul class="nav nav-tabs" id="languages-tab">
                        <?php foreach ($languages as $language): ?>
                            <li<?php if ($language->default) echo ' class="active"'; ?>>
                                <a href="#<?php echo CHtml::encode($language->code); ?>">
                                    <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png" alt="<?php echo CHtml::encode($language->name); ?>">
                                    <?php echo CHtml::encode($language->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="tab-content">
                        <?php foreach ($languages as $language): ?>
                            <div class="tab-pane<?php if ($language->default) echo ' active'; ?>" id="<?php echo CHtml::encode($language->code); ?>">
                                <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
                                    <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name"><?php echo Yii::t('app', 'Name'); ?></label>
                                    <div class="controls">
                                        <input type="text" class="input-xlarge" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][name]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['name']) : ''; ?>">
                                        <?php if ($model->getError('name')): ?>
                                            <p class="help-block"><?php echo $model->getError('name'); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="rtf-report <?php if ($model->type == ReportTemplate::TYPE_DOCX) echo "hide"; ?>">
                                    <div class="control-group <?php if ($model->getError('intro')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_intro"><?php echo Yii::t('app', 'Introduction'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_intro" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][intro]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['intro']) : ''; ?></textarea>
                                            <?php if ($model->getError('intro')): ?>
                                                <p class="help-block"><?php echo $model->getError('intro'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('securityLevelIntro')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_securityLevelIntro"><?php echo Yii::t('app', 'Security Level Introduction'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_securityLevelIntro" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][securityLevelIntro]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['securityLevelIntro']) : ''; ?></textarea>
                                            <?php if ($model->getError('securityLevelIntro')): ?>
                                                <p class="help-block"><?php echo $model->getError('securityLevelIntro'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('vulnDistributionIntro')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_vulnDistributionIntro"><?php echo Yii::t('app', 'Vuln Distribution Introduction'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_vulnDistributionIntro" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][vulnDistributionIntro]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['vulnDistributionIntro']) : ''; ?></textarea>
                                            <?php if ($model->getError('vulnDistributionIntro')): ?>
                                                <p class="help-block"><?php echo $model->getError('vulnDistributionIntro'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('degreeIntro')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_degreeIntro"><?php echo Yii::t('app', 'Degree of Fulfillment Introduction'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_degreeIntro" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][degreeIntro]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['degreeIntro']) : ''; ?></textarea>
                                            <?php if ($model->getError('degreeIntro')): ?>
                                                <p class="help-block"><?php echo $model->getError('degreeIntro'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('riskIntro')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_riskIntro"><?php echo Yii::t('app', 'Risk Matrix Introduction'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_riskIntro" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][riskIntro]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['riskIntro']) : ''; ?></textarea>
                                            <?php if ($model->getError('riskIntro')): ?>
                                                <p class="help-block"><?php echo $model->getError('riskIntro'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('reducedIntro')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_reducedIntro"><?php echo Yii::t('app', 'Reduced Vuln List Introduction'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_reducedIntro" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][reducedIntro]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['reducedIntro']) : ''; ?></textarea>
                                            <?php if ($model->getError('reducedIntro')): ?>
                                                <p class="help-block"><?php echo $model->getError('reducedIntro'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('highDescription')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_highDescription"><?php echo Yii::t('app', 'High Risk Description'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_highDescription" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][highDescription]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['highDescription']) : ''; ?></textarea>
                                            <?php if ($model->getError('highDescription')): ?>
                                                <p class="help-block"><?php echo $model->getError('highDescription'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('medDescription')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_medDescription"><?php echo Yii::t('app', 'Med Risk Description'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_medDescription" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][medDescription]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['medDescription']) : ''; ?></textarea>
                                            <?php if ($model->getError('medDescription')): ?>
                                                <p class="help-block"><?php echo $model->getError('medDescription'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('lowDescription')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_lowDescription"><?php echo Yii::t('app', 'Low Risk Description'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_lowDescription" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][lowDescription]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['lowDescription']) : ''; ?></textarea>
                                            <?php if ($model->getError('lowDescription')): ?>
                                                <p class="help-block"><?php echo $model->getError('lowDescription'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('noneDescription')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_noneDescription"><?php echo Yii::t('app', 'No Test Done Description'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_noneDescription" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][noneDescription]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['noneDescription']) : ''; ?></textarea>
                                            <?php if ($model->getError('noneDescription')): ?>
                                                <p class="help-block"><?php echo $model->getError('noneDescription'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('noVulnDescription')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_noVulnDescription"><?php echo Yii::t('app', 'No Vulnerability Description'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_noVulnDescription" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][noVulnDescription]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['noVulnDescription']) : ''; ?></textarea>
                                            <?php if ($model->getError('noVulnDescription')): ?>
                                                <p class="help-block"><?php echo $model->getError('noVulnDescription'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('infoDescription')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_infoDescription"><?php echo Yii::t('app', 'Info Description'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_infoDescription" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][infoDescription]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['infoDescription']) : ''; ?></textarea>
                                            <?php if ($model->getError('infoDescription')): ?>
                                                <p class="help-block"><?php echo $model->getError('infoDescription'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('vulnsIntro')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_vulnsIntro"><?php echo Yii::t('app', 'Vulns Introduction'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_vulnsIntro" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][vulnsIntro]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['vulnsIntro']) : ''; ?></textarea>
                                            <?php if ($model->getError('vulnsIntro')): ?>
                                                <p class="help-block"><?php echo $model->getError('vulnsIntro'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('infoChecksIntro')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_infoChecksIntro"><?php echo Yii::t('app', 'Info Checks Introduction'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_infoChecksIntro" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][infoChecksIntro]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['infoChecksIntro']) : ''; ?></textarea>
                                            <?php if ($model->getError('infoChecksIntro')): ?>
                                                <p class="help-block"><?php echo $model->getError('infoChecksIntro'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('appendix')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_appendix"><?php echo Yii::t('app', 'Appendix'); ?></label>
                                        <div class="controls">
                                            <textarea class="wysiwyg" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_appendix" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][appendix]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['appendix']) : ''; ?></textarea>
                                            <?php if ($model->getError('appendix')): ?>
                                                <p class="help-block"><?php echo $model->getError('appendix'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="control-group <?php if ($model->getError('footer')) echo 'error'; ?>">
                                        <label class="control-label" for="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_footer"><?php echo Yii::t('app', 'Footer'); ?></label>
                                        <div class="controls">
                                            <input type="text" class="input-xlarge" id="ReportTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_footer" name="ReportTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][footer]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['footer']) : ''; ?>">
                                            <?php if ($model->getError('footer')): ?>
                                                <p class="help-block"><?php echo $model->getError('footer'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <?php if (!$template->isNewRecord): ?>
                        <div class="rtf-report <?php if ($model->type == ReportTemplate::TYPE_DOCX) echo "hide"; ?>">
                            <div class="control-group">
                                <label class="control-label"><?php echo Yii::t('app', 'Header Image'); ?></label>
                                <div class="controls form-text">
                                    <div class="header-image" data-control-url="<?php echo $this->createUrl('reporttemplate/controlheaderimage'); ?>">
                                        <?php if ($template->header_image_path): ?>
                                            <img src="<?php echo $this->createUrl('reporttemplate/headerimage', array( 'id' => $template->id )); ?>" width="400">
                                        <?php else: ?>
                                            <?php echo Yii::t('app', 'No header image.'); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="file-input">
                                        <a href="#header-image"><?php echo Yii::t('app', 'Upload Header Image'); ?></a>
                                        <input type="file" name="ReportTemplateHeaderImageUploadForm[image]" data-upload-url="<?php echo $this->createUrl('reporttemplate/uploadheaderimage', array( 'id' => $template->id )); ?>">
                                    </div>

                                    <div class="upload-message hide"><?php echo Yii::t('app', 'Uploading...'); ?></div>

                                    <a class="delete-header-link<?php if (!$template->header_image_path) echo ' hide'; ?>" href="#delete-header-image" onclick="admin.reportTemplate.delHeaderImage(<?php echo $template->id; ?>);"><?php echo Yii::t('app', 'Delete Header Image'); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!$template->isNewRecord): ?>
                        <div class="docx-report <?php if ($model->type == ReportTemplate::TYPE_RTF) echo "hide"; ?>">
                            <div class="control-group">
                                <label class="control-label"><?php echo Yii::t("app", "File"); ?></label>
                                <div class="controls form-text">
                                    <div class="template-file" data-control-url="<?php echo $this->createUrl("reporttemplate/controlfile"); ?>">
                                        <span class="template-file-link">
                                            <?php if ($template->file_path): ?>
                                                <a href="<?php echo $this->createUrl("reporttemplate/file", array("id" => $template->id)); ?>"><?php echo Yii::t("app", "Download"); ?></a>
                                            <?php else: ?>
                                                <?php echo Yii::t("app", "No template file."); ?>
                                            <?php endif; ?>
                                        </span>

                                        <span class="delete-file-link<?php if (!$template->file_path) echo " hide"; ?>">
                                            or <a href="#delete-file" onclick="admin.reportTemplate.delTemplate(<?php echo $template->id; ?>);"><?php echo Yii::t("app", "Delete File"); ?></a>
                                        </span>
                                    </div>

                                    <div class="file-input">
                                        <a href="#file"><?php echo Yii::t("app", "Upload File"); ?></a>
                                        <input type="file" name="ReportTemplateFileUploadForm[file]" data-upload-url="<?php echo $this->createUrl("reporttemplate/uploadfile", array("id" => $template->id)); ?>">
                                    </div>

                                    <div class="upload-message hide"><?php echo Yii::t("app", "Uploading..."); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="span4">
                    <div class="rtf-report <?php if ($model->type == ReportTemplate::TYPE_DOCX) echo "hide"; ?>">
                        <div id="rtf-var-list-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#rtf-var-list');"><i class="icon-chevron-up"></i></div>
                        <h3><a href="#toggle" onclick="system.toggleBlock('#rtf-var-list');"><?php echo Yii::t("app", "Variable List"); ?></a></h3>

                        <div class="info-block" id="rtf-var-list">
                            <table class="table client-details">
                                <tr>
                                    <th>
                                        {client}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'Client name'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {project}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'Project name'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {year}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'Project year'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {deadline}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'Project deadline'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {admin}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'Project admin'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {rating}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'Project rating'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <br><hr>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {targets}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'Number of targets'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {target.list}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'List of targets'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {target.stats}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'List of targets with statistics'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {target.weakest}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'List of targets with a name of the weakest control'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {vuln.list}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'List of top 5 most dangerous vulnerabilities'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <br><hr>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {checks}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'Number of finished checks'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {checks.hi}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'Number of high risk checks'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {checks.med}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'Number of med risk checks'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {checks.lo}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'Number of low risk checks'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {checks.info}
                                    </th>
                                    <td>
                                        <?php echo Yii::t('app', 'Number of info rating checks'); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="docx-report <?php if ($model->type == ReportTemplate::TYPE_RTF) echo "hide"; ?>">
                        <div id="docx-var-list-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#docx-var-list');"><i class="icon-chevron-up"></i></div>
                        <h3><a href="#toggle" onclick="system.toggleBlock('#docx-var-list');"><?php echo Yii::t("app", "Variable List"); ?></a></h3>

                        <div class="info-block" id="docx-var-list">
                            <table class="table client-details">
                                <tr>
                                    <th>
                                        {client}
                                    </th>
                                    <td>
                                        <?php echo Yii::t("app", "Client name"); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Save"); ?></button>
        </div>
    </fieldset>
</form>

<script>
    $('#languages-tab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    $(function () {
        $(".wysiwyg").ckeditor();
        admin.reportTemplate.initHeaderImageUploadForm();
        admin.reportTemplate.initTemplateUploadForm();
    });
</script>