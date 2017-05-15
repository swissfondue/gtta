<link rel="stylesheet" type="text/css" href="/css/cvss.css">
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.ui.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.iframe-transport.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.fileupload.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/cvss/cvsscalc30_helptext.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/cvss/cvsscalc30.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/cvss/cvss.js"></script>
<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>

<?php if (User::checkRole(User::ROLE_USER)): ?>
    <div class="active-header">
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li>
                    <a href="<?php echo $this->createUrl('project/editchain', array('id' => $project->id, 'target' => $target->id)); ?>"><?php echo Yii::t('app', 'Check Chain'); ?></a>
                </li>
            </ul>
        </div>

        <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
    </div>
<?php else: ?>
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
<?php endif; ?>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($controls) > 0): ?>
                <div>
                    <table class="table control-header">
                        <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t("app", "Category"); ?></th>
                            <th class="stats"><?php echo Yii::t("app", "Risk Stats"); ?></th>
                            <th class="percent"><?php echo Yii::t("app", "Completed"); ?></th>
                            <th class="check-count"><?php echo Yii::t("app", "Checks"); ?></th>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <?php foreach ($controls as $control): ?>
                    <div id="control-<?php echo $control->id; ?>" class="control-header" data-type="control"
                         data-id="<?php echo $control->id; ?>"
                         data-checklist-url="<?php echo $this->createUrl("project/controlchecklist", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "control" => $control->id)); ?>">
                        <table class="table control-header">
                            <tbody>
                            <tr>
                                <td class="name">
                                    <?php if (User::checkRole(User::ROLE_USER)): ?>
                                        <a href="#control" data-type="control-link"
                                           data-id="<?php echo $control->id; ?>"
                                           onclick="user.check.toggleControl(<?php echo $control->id; ?>);"><?php echo CHtml::encode($control->localizedName); ?></a>
                                    <?php else: ?>
                                        <a href="#control" data-type="control-link"
                                           data-id="<?php echo $control->id; ?>"
                                           onclick="client.check.toggleControl(<?php echo $control->id; ?>);"><?php echo CHtml::encode($control->localizedName); ?></a>
                                    <?php endif; ?>
                                </td>
                                <td class="stats">
                                    <span class="high-risk"><?php echo $stats[$control->id]["highRisk"]; ?></span> /
                                    <span class="med-risk"><?php echo $stats[$control->id]["medRisk"]; ?></span> /
                                    <span class="low-risk"><?php echo $stats[$control->id]["lowRisk"]; ?></span> /
                                    <span class="info"><?php echo $stats[$control->id]["info"]; ?></span>
                                </td>
                                <td class="percent">
                                    <?php echo $stats[$control->id]["checks"] ? sprintf("%.0f", ($stats[$control->id]["finished"] / $stats[$control->id]["checks"]) * 100) : "0"; ?>
                                    % /
                                    <?php echo $stats[$control->id]["finished"]; ?>
                                </td>
                                <td class="check-count">
                                    <?php echo $stats[$control->id]["checks"]; ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="control-body hide" data-id="<?php echo $control->id; ?>">
                        <?php if (User::checkRole(User::ROLE_USER)): ?>
                            <div id="custom-template-<?php echo $control->id; ?>" class="check-header"
                                 data-type="custom-template" data-id="<?php echo $control->id; ?>"
                                 data-control-url="<?php echo $this->createUrl("project/controlcustomcheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id)); ?>">
                                <table class="check-header">
                                    <tbody>
                                    <tr>
                                        <td class="name">
                                            <a href="#custom-template"
                                               onclick="user.check.toggleCustomTemplate(<?php echo $control->id; ?>);"><?php echo Yii::t("app", "Custom Check"); ?></a>
                                        </td>
                                        <td class="status">
                                            &nbsp;
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="check-form hide" data-type="custom-template"
                                 data-id="<?php echo $control->id; ?>"
                                 data-save-url="<?php echo $this->createUrl("project/savecustomcheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id)); ?>">
                                <table class="table check-form">
                                    <tbody>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Reference"); ?>
                                        </th>
                                        <td class="text">
                                            CUSTOM-CHECK
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Name"); ?>
                                        </th>
                                        <td>
                                            <input type="text" class="input-xlarge"
                                                   name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[name]"
                                                   id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_name"
                                                   value="" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Background Info"); ?>
                                        </th>
                                        <td>
                                            <textarea
                                                name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[backgroundInfo]"
                                                class="max-width wysiwyg" rows="10"
                                                id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_backgroundInfo" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Question"); ?>
                                        </th>
                                        <td>
                                            <textarea
                                                name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[question]"
                                                class="max-width wysiwyg" rows="10"
                                                id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_question" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Result"); ?>
                                        </th>
                                        <td>
                                            <textarea
                                                name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[result]"
                                                class="max-width" rows="10"
                                                id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_result" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                            <br>

                                                <span class="help-block pull-right">
                                                    <a class="btn btn-default" href="#editor"
                                                       onclick="user.check.toggleEditor('TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_result');">
                                                        <span class="glyphicon glyphicon-edit"></span>
                                                        <?php echo Yii::t("app", "WYSIWYG"); ?>
                                                    </a>
                                                </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Solution Title"); ?>
                                        </th>
                                        <td>
                                            <input type="text" class="input-xlarge"
                                                   name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[solutionTitle]"
                                                   id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_solutionTitle"
                                                   value="" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Solution"); ?>
                                        </th>
                                        <td>
                                            <textarea
                                                name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[solution]"
                                                class="max-width wysiwyg" rows="10"
                                                id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_solution" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Result Rating"); ?>
                                        </th>
                                        <td class="text">
                                            <?php if (isset($check)): ?>
                                                <button class="btn" onclick="user.check.cvss(<?php echo $check->id; ?>);">
                                                    Set CVSS 3.0 Vector
                                                </button>
                                                <br>
                                                <br>
                                            <?php endif; ?>
                                            <ul class="rating">
                                                <?php foreach (TargetCustomCheck::getValidRatings() as $rating): ?>
                                                    <li>
                                                        <label class="radio">
                                                            <input type="radio"
                                                                   name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[rating]"
                                                                   value="<?php echo $rating; ?>" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                                            <?php echo $ratings[$rating]; ?>
                                                        </label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Create New Check"); ?>
                                        </th>
                                        <td class="text">
                                            <input type="checkbox"
                                                   name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[createCheck]"
                                                   value="1" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                        </td>
                                    </tr>

                                    <?php if (User::checkRole(User::ROLE_USER)): ?>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                <button class="btn"
                                                        onclick="user.check.saveCustomTemplate(<?php echo $control->id; ?>);"><?php echo Yii::t("app", "Save"); ?></button>
                                                &nbsp;
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <?php foreach ($control->customChecks as $custom): ?>
                            <div id="custom-check-<?php echo $custom->id; ?>" class="check-header"
                                 data-type="custom-check" data-id="<?php echo $custom->id; ?>"
                                 data-control-url="<?php echo $this->createUrl("project/controlcustomcheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id)); ?>">
                                <table class="check-header">
                                    <tbody>
                                    <tr>
                                        <td class="name">
                                            <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                <a href="#custom-check"
                                                   onclick="user.check.toggleCustomCheck(<?php echo $custom->id; ?>);"><?php echo $custom->name ? CHtml::encode($custom->name) : "CUSTOM-CHECK-" . $custom->reference; ?></a>
                                                <a href="#delete" title="<?php echo Yii::t("app", "Delete"); ?>"
                                                   onclick="user.check.deleteCustom(<?php echo $custom->id; ?>);"><i
                                                        class="icon icon-remove"></i></a>
                                            <?php else: ?>
                                                <a href="#custom-check"
                                                   onclick="client.check.toggleCustomCheck(<?php echo $custom->id; ?>);"><?php echo $custom->name ? CHtml::encode($custom->name) : "CUSTOM-CHECK-" . $custom->reference; ?></a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="status">
                                            <?php echo $this->renderPartial("partial/check-rating", ["check" => $custom]); ?>
                                        </td>
                                        <?php if (User::checkRole(User::ROLE_USER)): ?>
                                            <td class="actions">
                                                <a href="#reset" title="<?php echo Yii::t("app", "Reset"); ?>"
                                                   onclick="user.check.resetCustom(<?php echo $custom->id; ?>);"><i
                                                        class="icon icon-refresh"></i></a>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="check-form hide" data-type="custom-check" data-id="<?php echo $custom->id; ?>"
                                 data-save-url="<?php echo $this->createUrl("project/savecustomcheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id)); ?>">
                                <table class="table check-form">
                                    <tbody>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Reference"); ?>
                                        </th>
                                        <td class="text">
                                            <?php echo "CUSTOM-CHECK-" . $custom->reference; ?>
                                            <input type="hidden" name="TargetCustomCheckEditForm_<?php echo $custom->id ?>[last_modified]" id="TargetCustomCheckEditForm_<?php echo $custom->id ?>[last_modified]" value="<?php echo $custom->last_modified; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Name"); ?>
                                        </th>
                                        <td>
                                            <input type="text" class="input-xlarge"
                                                   name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[name]"
                                                   id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_name"
                                                   value="<?php echo CHtml::encode($custom->name); ?>" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Background Info"); ?>
                                        </th>
                                        <td>
                                            <textarea
                                                name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[backgroundInfo]"
                                                class="max-width wysiwyg" rows="10"
                                                id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_backgroundInfo" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($custom->background_info); ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Question"); ?>
                                        </th>
                                        <td>
                                            <textarea
                                                name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[question]"
                                                class="max-width wysiwyg" rows="10"
                                                id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_question" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($custom->question); ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Result"); ?>
                                        </th>
                                        <td>
                                            <textarea
                                                name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[result]"
                                                class="max-width" rows="10"
                                                id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_result" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($custom->result); ?></textarea>
                                            <br>

                                                <span class="help-block pull-right">
                                                    <a class="btn btn-default" href="#editor"
                                                       onclick="user.check.toggleEditor('TargetCustomCheckEditForm_<?php echo $custom->id; ?>_result');">
                                                        <span class="glyphicon glyphicon-edit"></span>
                                                        <?php echo Yii::t("app", "WYSIWYG"); ?>
                                                    </a>
                                                </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Solution Title"); ?>
                                        </th>
                                        <td>
                                            <input type="text" class="input-xlarge"
                                                   name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[solutionTitle]"
                                                   id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_solutionTitle"
                                                   value="<?php echo CHtml::encode($custom->solution_title); ?>" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Solution"); ?>
                                        </th>
                                        <td>
                                            <textarea
                                                name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[solution]"
                                                class="max-width wysiwyg" rows="10"
                                                id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_solution" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($custom->solution); ?></textarea>
                                        </td>
                                    </tr>
                                    <?php if (User::checkRole(User::ROLE_USER) || $custom->attachments): ?>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Attachments"); ?>
                                            </th>
                                            <td class="text">
                                                <div class="file-input"
                                                     id="upload-custom-link-<?php echo $custom->id; ?>">
                                                    <a href="#attachment"><?php echo Yii::t("app", "New Attachment"); ?></a>
                                                    <input type="file"
                                                           name="TargetCustomCheckAttachmentUploadForm[attachment]"
                                                           accept="image/*,.txt" data-id="<?php echo $custom->id; ?>"
                                                           data-upload-url="<?php echo $this->createUrl("project/uploadcustomattachment", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $custom->id)); ?>">
                                                </div>

                                                <div class="upload-message hide"
                                                     id="upload-custom-message-<?php echo $custom->id; ?>"><?php echo Yii::t("app", "Uploading..."); ?></div>

                                                <table
                                                    class="table attachment-list<?php if (!$custom->attachments) echo " hide"; ?>">
                                                    <tbody>
                                                    <?php if ($custom->attachments): ?>
                                                        <?php foreach ($custom->attachments as $attachment): ?>
                                                            <tr data-path="<?php echo $attachment->path; ?>"
                                                                data-control-url="<?php echo $this->createUrl("project/controlcustomattachment"); ?>">
                                                                <td class="info">
                                                                            <span contenteditable="true"
                                                                                  class="single-line title"
                                                                                  onblur="$(this).siblings('input').val($(this).text());">
                                                                                <?php echo CHtml::encode($attachment->title); ?>
                                                                            </span>
                                                                    <input type="hidden"
                                                                           name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[attachmentTitles][]"
                                                                           data-path="<?php echo $attachment->path; ?>"
                                                                           value="<?php echo CHtml::encode($attachment->title); ?>">
                                                                </td>
                                                                <td class="actions">
                                                                    <a href="<?php echo $this->createUrl("project/customattachment", array("path" => $attachment->path)); ?>"
                                                                       title="<?php echo Yii::t("app", "Download"); ?>"><i
                                                                            class="icon icon-download"></i></a>
                                                                    <a href="#del"
                                                                       title="<?php echo Yii::t("app", "Delete"); ?>"
                                                                       onclick="user.check.delCustomAttachment('<?php echo $attachment->path; ?>');"><i
                                                                            class="icon icon-remove"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Result Rating"); ?>
                                        </th>
                                        <td class="text">
                                            <?php if (isset($check)): ?>
                                                <button class="btn" onclick="user.check.cvss(<?php echo $check->id; ?>);">
                                                    Set CVSS 3.0 Vector
                                                </button>
                                                <br>
                                                <br>
                                            <?php endif; ?>

                                            <ul class="rating">
                                                <?php foreach (TargetCustomCheck::getValidRatings() as $rating): ?>
                                                    <li>
                                                        <label class="radio">
                                                            <input type="radio"
                                                                   name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[rating]"
                                                                   value="<?php echo $rating; ?>" <?php if ($custom->rating == $rating) echo "checked"; ?> <?php if (User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                                            <?php echo $ratings[$rating]; ?>
                                                        </label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Create New Check"); ?>
                                        </th>
                                        <td class="text">
                                            <input type="checkbox"
                                                   name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[createCheck]"
                                                   value="1" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                        </td>
                                    </tr>

                                    <?php if (User::checkRole(User::ROLE_USER)): ?>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                <button class="btn"
                                                        onclick="user.check.saveCustom(<?php echo $custom->id; ?>);"><?php echo Yii::t("app", "Save"); ?></button>
                                                &nbsp;
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php echo Yii::t("app", "No checks in this category."); ?>
            <?php endif; ?>
        </div>
        <div class="span4">
            <?php
            echo $this->renderPartial(
                "partial/right-block", array(
                "quickTargets" => $quickTargets,
                "project" => $project,
                "category" => $category,
                "target" => $target
            )
            );
            ?>
        </div>
    </div>
</div>

<script>
    var ratings = {
        <?php
        $ratingNames = array();

        foreach ($ratings as $k => $v) {
            $class = null;
            switch ($k) {
                case TargetCheck::RATING_INFO:
                    $class = "label-info";
                    break;
                case TargetCheck::RATING_LOW_RISK:
                    $class = "label-low-risk";
                    break;
                case TargetCheck::RATING_MED_RISK:
                    $class = "label-med-risk";
                    break;
                case TargetCheck::RATING_HIGH_RISK:
                    $class = "label-high-risk";
                    break;
            }
            $ratingNames[] = $k . ":" . json_encode(
                    array(
                        "text" => CHtml::encode($v),
                        "classN" => $class
                    )
                );
        }

        echo implode(",", $ratingNames);
        ?>
    };

    <?php if (User::checkRole(User::ROLE_USER)): ?>
    $(function () {
        user.check.initTargetCustomCheckAttachmentUploadForms();

        setInterval(function () {
            user.check.getRunningChecks("<?= $this->createUrl("project/runningchecks"); ?>", <?= $target->id ?>);
        }, 5000);

        setInterval(function () {
            user.check.update("<?php echo $this->createUrl("project/updatechecks", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id)); ?>");
        }, 1000);

        $(".wysiwyg").ckeditor();

        <?php if ($controlToOpen): ?>
        user.check.toggleControl(<?php print $controlToOpen; ?>, function () {
            <?php if ($checkToOpen): ?>
            user.check.toggle(<?php print $checkToOpen; ?>);
            <?php endif; ?>
        });
        <?php endif; ?>
    });
    <?php endif; ?>
</script>

<div class="modal modal-cvss hide" id="cvss-dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3><?php echo Yii::t("app", "CVSS Version 3.0 Calculator"); ?></h3>
    </div>
    <div class="modal-body">

    <!-- CVSS Calculator content start -- add a <base href="http://www.first.org" /> to the head for offline editing -->

    <!--
      Copyright (c) 2015, FIRST.ORG, INC.
      All rights reserved.

      Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
      following conditions are met:
      1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following
         disclaimer.
      2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the
         following disclaimer in the documentation and/or other materials provided with the distribution.
      3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote
         products derived from this software without specific prior written permission.

      THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
      INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
      DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
      SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
      SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
      WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
      OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
    -->

    <!-- Window width should be 675px for a good fit. -->
    <form action="#">
        <fieldset style="background:#090; color:#ffffff; border-radius:10px">
            <p id="vector">Vector String -
                <span class="needBaseMetrics">select values for all base metrics to generate a vector</span>
                <input id="vectorString" type="text" readonly></input>
            </p>
        </fieldset>

        <fieldset id="baseMetricGroup">
            <legend id="baseMetricGroup_Legend">Base Score</legend>

            <div class="col" style="float: left; height: auto;">

                <div class="metric">
                    <h3 id="AV_Heading">Attack Vector (AV)</h3>
                    <input type="radio" name="AV" value="N" id="AV_N"/><label for="AV_N" id="AV_N_Label">Network
                        (N)</label>
                    <input type="radio" name="AV" value="A" id="AV_A"/><label for="AV_A" id="AV_A_Label">Adjacent
                        (A)</label>
                    <input type="radio" name="AV" value="L" id="AV_L"/><label for="AV_L" id="AV_L_Label">Local
                        (L)</label>
                    <input type="radio" name="AV" value="P" id="AV_P"/><label for="AV_P" id="AV_P_Label">Physical
                        (P)</label>
                </div>

                <div class="metric">
                    <h3 id="AC_Heading">Attack Complexity (AC)</h3>
                    <input type="radio" name="AC" value="L" id="AC_L"/><label for="AC_L" id="AC_L_Label">Low (L)</label>
                    <input type="radio" name="AC" value="H" id="AC_H"/><label for="AC_H" id="AC_H_Label">High
                        (H)</label>
                </div>

                <div class="metric">
                    <h3 id="PR_Heading">Privileges Required (PR)</h3>
                    <input type="radio" name="PR" value="N" id="PR_N"/><label for="PR_N" id="PR_N_Label">None
                        (N)</label>
                    <input type="radio" name="PR" value="L" id="PR_L"/><label for="PR_L" id="PR_L_Label">Low (L)</label>
                    <input type="radio" name="PR" value="H" id="PR_H"/><label for="PR_H" id="PR_H_Label">High
                        (H)</label>
                </div>

                <div class="metric">
                    <h3 id="UI_Heading">User Interaction (UI)</h3>
                    <input type="radio" name="UI" value="N" id="UI_N"/><label for="UI_N" id="UI_N_Label">None
                        (N)</label>
                    <input type="radio" name="UI" value="R" id="UI_R"/><label for="UI_R" id="UI_R_Label">Required
                        (R)</label>
                </div>

            </div>


            <div class="col" style="float: right; height: auto;">

                <div class="metric">
                    <h3 id="S_Heading">Scope (S)</h3>
                    <input type="radio" name="S" value="U" id="S_U"/><label for="S_U" id="S_U_Label">Unchanged
                        (U)</label>
                    <input type="radio" name="S" value="C" id="S_C"/><label for="S_C" id="S_C_Label">Changed (C)</label>
                </div>

                <div class="metric">
                    <h3 id="C_Heading">Confidentiality (C)</h3>
                    <input type="radio" name="C" value="N" id="C_N"/><label for="C_N" id="C_N_Label">None (N)</label>
                    <input type="radio" name="C" value="L" id="C_L"/><label for="C_L" id="C_L_Label">Low (L)</label>
                    <input type="radio" name="C" value="H" id="C_H"/><label for="C_H" id="C_H_Label">High (H)</label>
                </div>

                <div class="metric">
                    <h3 id="I_Heading">Integrity (I)</h3>
                    <input type="radio" name="I" value="N" id="I_N"/><label for="I_N" id="I_N_Label">None (N)</label>
                    <input type="radio" name="I" value="L" id="I_L"/><label for="I_L" id="I_L_Label">Low (L)</label>
                    <input type="radio" name="I" value="H" id="I_H"/><label for="I_H" id="I_H_Label">High (H)</label>
                </div>

                <div class="metric">
                    <h3 id="A_Heading">Availability (A)</h3>
                    <input type="radio" name="A" value="N" id="A_N"/><label for="A_N" id="A_N_Label">None (N)</label>
                    <input type="radio" name="A" value="L" id="A_L"/><label for="A_L" id="A_L_Label">Low (L)</label>
                    <input type="radio" name="A" value="H" id="A_H"/><label for="A_H" id="A_H_Label">High (H)</label>
                </div>

            </div>


            <div class="scoreRating">
                <p class="needBaseMetrics">Select values for all base metrics to generate score</p>
                <span id="baseMetricScore"></span>
                <span id="baseSeverity"></span>
            </div>
        </fieldset>
        <div class="end"></div>

        <fieldset id="temporalMetricGroup">
            <legend id="temporalMetricGroup_Legend">Temporal Score</legend>

            <div class="col" style="float: left; height: auto;">

                <div class="metric">
                    <h3 id="E_Heading">Exploit Code Maturity (E)</h3>
                    <input type="radio" name="E" value="X" id="E_X" checked/><label for="E_X" id="E_X_Label">Not Defined
                        (X)</label>
                    <input type="radio" name="E" value="U" id="E_U"/><label for="E_U" id="E_U_Label">Unproven
                        (U)</label>
                    <input type="radio" name="E" value="P" id="E_P"/><label for="E_P" id="E_P_Label">Proof-of-Concept
                        (P)</label>
                    <input type="radio" name="E" value="F" id="E_F"/><label for="E_F" id="E_F_Label">Functional
                        (F)</label>
                    <input type="radio" name="E" value="H" id="E_H"/><label for="E_H" id="E_H_Label">High (H)</label>
                </div>

                <div class="metric">
                    <h3 id="RL_Heading">Remediation Level (RL)</h3>
                    <input type="radio" name="RL" value="X" id="RL_X" checked/><label for="RL_X" id="RL_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="RL" value="O" id="RL_O"/><label for="RL_O" id="RL_O_Label">Official Fix
                        (O)</label>
                    <input type="radio" name="RL" value="T" id="RL_T"/><label for="RL_T" id="RL_T_Label">Temporary Fix
                        (T)</label>
                    <input type="radio" name="RL" value="W" id="RL_W"/><label for="RL_W" id="RL_W_Label">Workaround
                        (W)</label>
                    <input type="radio" name="RL" value="U" id="RL_U"/><label for="RL_U" id="RL_U_Label">Unavailable
                        (U)</label>
                </div>

                <div class="metric">
                    <h3 id="RC_Heading">Report Confidence (RC)</h3>
                    <input type="radio" name="RC" value="X" id="RC_X" checked/><label for="RC_X" id="RC_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="RC" value="U" id="RC_U"/><label for="RC_U" id="RC_U_Label">Unknown
                        (U)</label>
                    <input type="radio" name="RC" value="R" id="RC_R"/><label for="RC_R" id="RC_R_Label">Reasonable
                        (R)</label>
                    <input type="radio" name="RC" value="C" id="RC_C"/><label for="RC_C" id="RC_C_Label">Confirmed
                        (C)</label>
                </div>

            </div>

            <div class="scoreRating">
                <p class="needBaseMetrics">Select values for all base metrics to generate score</p>
                <span id="temporalMetricScore"></span>
                <span id="temporalSeverity"></span>
            </div>
        </fieldset>
        <div class="end"></div>

        <fieldset id="environmentalMetricGroup">
            <legend id="environmentalMetricGroup_Legend">Environmental Score</legend>

            <div class="col" style="float: left; height: auto;">

                <div class="metric">
                    <h3 id="CR_Heading">Confidentiality Requirement (CR)</h3>
                    <input type="radio" name="CR" value="X" id="CR_X" checked/><label for="CR_X" id="CR_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="CR" value="L" id="CR_L"/><label for="CR_L" id="CR_L_Label">Low (L)</label>
                    <input type="radio" name="CR" value="M" id="CR_M"/><label for="CR_M" id="CR_M_Label">Medium
                        (M)</label>
                    <input type="radio" name="CR" value="H" id="CR_H"/><label for="CR_H" id="CR_H_Label">High
                        (H)</label>
                </div>

                <div class="metric">
                    <h3 id="IR_Heading">Integrity Requirement (IR)</h3>
                    <input type="radio" name="IR" value="X" id="IR_X" checked/><label for="IR_X" id="IR_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="IR" value="L" id="IR_L"/><label for="IR_L" id="IR_L_Label">Low (L)</label>
                    <input type="radio" name="IR" value="M" id="IR_M"/><label for="IR_M" id="IR_M_Label">Medium
                        (M)</label>
                    <input type="radio" name="IR" value="H" id="IR_H"/><label for="IR_H" id="IR_H_Label">High
                        (H)</label>
                </div>

                <div class="metric">
                    <h3 id="AR_Heading">Availability Requirement (AR)</h3>
                    <input type="radio" name="AR" value="X" id="AR_X" checked/><label for="AR_X" id="AR_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="AR" value="L" id="AR_L"/><label for="AR_L" id="AR_L_Label">Low (L)</label>
                    <input type="radio" name="AR" value="M" id="AR_M"/><label for="AR_M" id="AR_M_Label">Medium
                        (M)</label>
                    <input type="radio" name="AR" value="H" id="AR_H"/><label for="AR_H" id="AR_H_Label">High
                        (H)</label>
                </div>
            </div>

            <div class="col" style="float: right; height: auto;">
                <div class="metric">
                    <h3 id="MAV_Heading">Modified Attack Vector (MAV)</h3>
                    <input type="radio" name="MAV" value="X" id="MAV_X" checked/><label for="MAV_X" id="MAV_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="MAV" value="N" id="MAV_N"/><label for="MAV_N"
                                                                                id="MAV_N_Label">Network</label>
                    <input type="radio" name="MAV" value="A" id="MAV_A"/><label for="MAV_A" id="MAV_A_Label">Adjacent
                        Network</label>
                    <input type="radio" name="MAV" value="L" id="MAV_L"/><label for="MAV_L"
                                                                                id="MAV_L_Label">Local</label>
                    <input type="radio" name="MAV" value="P" id="MAV_P"/><label for="MAV_P"
                                                                                id="MAV_P_Label">Physical</label>
                </div>

                <div class="metric">
                    <h3 id="MAC_Heading">Modified Attack Complexity (MAC)</h3>
                    <input type="radio" name="MAC" value="X" id="MAC_X" checked/><label for="MAC_X" id="MAC_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="MAC" value="L" id="MAC_L"/><label for="MAC_L" id="MAC_L_Label">Low</label>
                    <input type="radio" name="MAC" value="H" id="MAC_H"/><label for="MAC_H"
                                                                                id="MAC_H_Label">High</label>
                </div>

                <div class="metric">
                    <h3 id="MPR_Heading">Modified Privileges Required (MPR)</h3>
                    <input type="radio" name="MPR" value="X" id="MPR_X" checked/><label for="MPR_X" id="MPR_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="MPR" value="N" id="MPR_N"/><label for="MPR_N"
                                                                                id="MPR_N_Label">None</label>
                    <input type="radio" name="MPR" value="L" id="MPR_L"/><label for="MPR_L" id="MPR_L_Label">Low</label>
                    <input type="radio" name="MPR" value="H" id="MPR_H"/><label for="MPR_H"
                                                                                id="MPR_H_Label">High</label>
                </div>

                <div class="metric">
                    <h3 id="MUI_Heading">Modified User Interaction (MUI)</h3>
                    <input type="radio" name="MUI" value="X" id="MUI_X" checked/><label for="MUI_X" id="MUI_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="MUI" value="N" id="MUI_N"/><label for="MUI_N"
                                                                                id="MUI_N_Label">None</label>
                    <input type="radio" name="MUI" value="R" id="MUI_R"/><label for="MUI_R"
                                                                                id="MUI_R_Label">Required</label>
                </div>

                <div class="metric">
                    <h3 id="MS_Heading">Modified Scope (MS)</h3>
                    <input type="radio" name="MS" value="X" id="MS_X" checked/><label for="MS_X" id="MS_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="MS" value="U" id="MS_U"/><label for="MS_U"
                                                                              id="MS_U_Label">Unchanged</label>
                    <input type="radio" name="MS" value="C" id="MS_C"/><label for="MS_C" id="MS_C_Label">Changed</label>
                </div>

                <div class="metric">
                    <h3 id="MC_Heading">Modified Confidentiality (MC)</h3>
                    <input type="radio" name="MC" value="X" id="MC_X" checked/><label for="MC_X" id="MC_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="MC" value="N" id="MC_N"/><label for="MC_N" id="MC_N_Label">None</label>
                    <input type="radio" name="MC" value="L" id="MC_L"/><label for="MC_L" id="MC_L_Label">Low</label>
                    <input type="radio" name="MC" value="H" id="MC_H"/><label for="MC_H" id="MC_H_Label">High</label>
                </div>

                <div class="metric">
                    <h3 id="MI_Heading">Modified Integrity (MI)</h3>
                    <input type="radio" name="MI" value="X" id="MI_X" checked/><label for="MI_X" id="MI_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="MI" value="N" id="MI_N"/><label for="MI_N" id="MI_N_Label">None</label>
                    <input type="radio" name="MI" value="L" id="MI_L"/><label for="MI_L" id="MI_L_Label">Low</label>
                    <input type="radio" name="MI" value="H" id="MI_H"/><label for="MI_H" id="MI_H_Label">High</label>
                </div>

                <div class="metric">
                    <h3 id="MA_Heading">Modified Availability (MA)</h3>
                    <input type="radio" name="MA" value="X" id="MA_X" checked/><label for="MA_X" id="MA_X_Label">Not
                        Defined (X)</label>
                    <input type="radio" name="MA" value="N" id="MA_N"/><label for="MA_N" id="MA_N_Label">None</label>
                    <input type="radio" name="MA" value="L" id="MA_L"/><label for="MA_L" id="MA_L_Label">Low</label>
                    <input type="radio" name="MA" value="H" id="MA_H"/><label for="MA_H" id="MA_H_Label">High</label>
                </div>
            </div>

            <div class="scoreRating">
                <p class="needBaseMetrics">Select values for all base metrics to generate score</p>
                <span id="environmentalMetricScore"></span>
                <span id="environmentalSeverity"></span>
            </div>
        </fieldset>
        <div class="end"></div>
    </form>
    <!-- CVSS Calculator end -->

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal"><?php echo Yii::t("app", "Cancel"); ?></button>
        <button id="add-button" class="btn btn-primary" onclick="user.check.cvssSet();"><?php echo Yii::t("app", "Set CVSS 3.0 Vector"); ?></button>
    </div>
</div>
