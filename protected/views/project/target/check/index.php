<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.ui.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.iframe-transport.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.fileupload.js"></script>
<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

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
                    <div id="control-<?php echo $control->id; ?>" class="control-header" data-type="control" data-id="<?php echo $control->id; ?>" data-checklist-url="<?php echo $this->createUrl("project/controlchecklist", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "control" => $control->id)); ?>">
                        <table class="table control-header">
                            <tbody>
                                <tr>
                                    <td class="name">
                                        <?php if (User::checkRole(User::ROLE_USER)): ?>
                                            <a href="#control" data-type="control-link" data-id="<?php echo $control->id; ?>" onclick="user.check.toggleControl(<?php echo $control->id; ?>);"><?php echo CHtml::encode($control->localizedName); ?></a>
                                        <?php else: ?>
                                            <a href="#control" data-type="control-link" data-id="<?php echo $control->id; ?>" onclick="client.check.toggleControl(<?php echo $control->id; ?>);"><?php echo CHtml::encode($control->localizedName); ?></a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="stats">
                                        <span class="high-risk"><?php echo $stats[$control->id]["highRisk"]; ?></span> /
                                        <span class="med-risk"><?php echo $stats[$control->id]["medRisk"]; ?></span> /
                                        <span class="low-risk"><?php echo $stats[$control->id]["lowRisk"]; ?></span> /
                                        <span class="info"><?php echo $stats[$control->id]["info"]; ?></span>
                                    </td>
                                    <td class="percent">
                                        <?php echo $stats[$control->id]["checks"] ? sprintf("%.0f", ($stats[$control->id]["finished"] / $stats[$control->id]["checks"]) * 100) : "0"; ?>% /
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
                            <div id="custom-template-<?php echo $control->id; ?>" class="check-header" data-type="custom-template" data-id="<?php echo $control->id; ?>" data-control-url="<?php echo $this->createUrl("project/controlcustomcheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id)); ?>">
                                <table class="check-header">
                                    <tbody>
                                        <tr>
                                            <td class="name">
                                                <a href="#custom-template" onclick="user.check.toggleCustomTemplate(<?php echo $control->id; ?>);"><?php echo Yii::t("app", "Custom Check"); ?></a>
                                            </td>
                                            <td class="status">
                                                &nbsp;
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="check-form hide" data-type="custom-template" data-id="<?php echo $control->id; ?>" data-save-url="<?php echo $this->createUrl("project/savecustomcheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id)); ?>">
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
                                                <input type="text" class="input-xlarge" name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[name]" id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_name" value="" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Background Info"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[backgroundInfo]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_backgroundInfo" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Question"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[question]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_question" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Result"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[result]" class="max-width" rows="10" id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_result" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                                <br>

                                                <span class="help-block pull-right">
                                                    <a class="btn btn-default" href="#editor" onclick="user.check.toggleEditor('TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_result');">
                                                        <span class="glyphicon glyphicon-edit"></span>
                                                        <?php echo Yii::t("app", "WYSIWYG"); ?>
                                                    </a>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php if ($this->_system->checklist_poc): ?>
                                            <tr>
                                                <th>
                                                    <?php echo Yii::t("app", "PoC"); ?>
                                                </th>
                                                <td>
                                                    <textarea name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[poc]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_poc" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($this->_system->checklist_links): ?>
                                            <tr>
                                                <th>
                                                    <?php echo Yii::t("app", "Links"); ?>
                                                </th>
                                                <td>
                                                    <textarea name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[links]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_links" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Solution Title"); ?>
                                            </th>
                                            <td>
                                                <input type="text" class="input-xlarge" name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[solutionTitle]" id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_solutionTitle" value="" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Solution"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[solution]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>_solution" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Result Rating"); ?>
                                            </th>
                                            <td class="text">
                                                <ul class="rating">
                                                    <?php foreach (TargetCustomCheck::getValidRatings() as $rating): ?>
                                                        <li>
                                                            <label class="radio">
                                                                <input type="radio" name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[rating]" value="<?php echo $rating; ?>" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
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
                                                <input type="checkbox" name="TargetCustomCheckTemplateEditForm_<?php echo $control->id; ?>[createCheck]" value="1" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                            </td>
                                        </tr>

                                        <?php if (User::checkRole(User::ROLE_USER)): ?>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>
                                                    <button class="btn" onclick="user.check.saveCustomTemplate(<?php echo $control->id; ?>);"><?php echo Yii::t("app", "Save"); ?></button>&nbsp;
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <?php foreach ($control->customChecks as $custom): ?>
                            <div id="custom-check-<?php echo $custom->id; ?>" class="check-header" data-type="custom-check" data-id="<?php echo $custom->id; ?>" data-control-url="<?php echo $this->createUrl("project/controlcustomcheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id)); ?>">
                                <table class="check-header">
                                    <tbody>
                                        <tr>
                                            <td class="name">
                                                <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                    <a href="#custom-check" onclick="user.check.toggleCustomCheck(<?php echo $custom->id; ?>);"><?php echo $custom->name ? CHtml::encode($custom->name) : "CUSTOM-CHECK-" . $custom->reference; ?></a>
                                                    <a href="#delete" title="<?php echo Yii::t("app", "Delete"); ?>" onclick="user.check.deleteCustom(<?php echo $custom->id; ?>);"><i class="icon icon-remove"></i></a>
                                                <?php else: ?>
                                                    <a href="#custom-check" onclick="client.check.toggleCustomCheck(<?php echo $custom->id; ?>);"><?php echo $custom->name ? CHtml::encode($custom->name) : "CUSTOM-CHECK-" . $custom->reference; ?></a>
                                                <?php endif; ?>
                                            </td>
                                            <td class="status">
                                                <?php
                                                    switch ($custom->rating) {
                                                        case TargetCustomCheck::RATING_INFO:
                                                            echo "<span class=\"label label-info\">" . $ratings[TargetCustomCheck::RATING_INFO] . "</span>";
                                                            break;

                                                        case TargetCustomCheck::RATING_LOW_RISK:
                                                            echo "<span class=\"label label-low-risk\">" . $ratings[TargetCustomCheck::RATING_LOW_RISK] . "</span>";
                                                            break;

                                                        case TargetCustomCheck::RATING_MED_RISK:
                                                            echo "<span class=\"label label-med-risk\">" . $ratings[TargetCustomCheck::RATING_MED_RISK] . "</span>";
                                                            break;

                                                        case TargetCustomCheck::RATING_HIGH_RISK:
                                                            echo "<span class=\"label label-high-risk\">" . $ratings[TargetCustomCheck::RATING_HIGH_RISK] . "</span>";
                                                            break;

                                                        default:
                                                            echo "<span class=\"label\">" . $ratings[$custom->rating] . "</span>";
                                                            break;
                                                    }
                                                ?>
                                            </td>
                                            <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                <td class="actions">
                                                    <a href="#reset" title="<?php echo Yii::t("app", "Reset"); ?>" onclick="user.check.resetCustom(<?php echo $custom->id; ?>);"><i class="icon icon-refresh"></i></a>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="check-form hide" data-type="custom-check" data-id="<?php echo $custom->id; ?>" data-save-url="<?php echo $this->createUrl("project/savecustomcheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id)); ?>">
                                <table class="table check-form">
                                    <tbody>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Reference"); ?>
                                            </th>
                                            <td class="text">
                                                <?php echo "CUSTOM-CHECK-" . $custom->reference; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Name"); ?>
                                            </th>
                                            <td>
                                                <input type="text" class="input-xlarge" name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[name]" id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_name" value="<?php echo CHtml::encode($custom->name); ?>" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Background Info"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[backgroundInfo]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_backgroundInfo" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($custom->background_info); ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Question"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[question]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_question" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($custom->question); ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Result"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[result]" class="max-width" rows="10" id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_result" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($custom->result); ?></textarea>
                                                <br>

                                                <span class="help-block pull-right">
                                                    <a class="btn btn-default" href="#editor" onclick="user.check.toggleEditor('TargetCustomCheckEditForm_<?php echo $custom->id; ?>_result');">
                                                        <span class="glyphicon glyphicon-edit"></span>
                                                        <?php echo Yii::t("app", "WYSIWYG"); ?>
                                                    </a>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php if ($this->_system->checklist_poc): ?>
                                            <tr>
                                                <th>
                                                    <?php echo Yii::t("app", "PoC"); ?>
                                                </th>
                                                <td>
                                                    <textarea name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[poc]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_poc" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($custom->poc); ?></textarea>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($this->_system->checklist_links): ?>
                                            <tr>
                                                <th>
                                                    <?php echo Yii::t("app", "Links"); ?>
                                                </th>
                                                <td>
                                                    <textarea name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[links]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_links" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($custom->links); ?></textarea>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Solution Title"); ?>
                                            </th>
                                            <td>
                                                <input type="text" class="input-xlarge" name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[solutionTitle]" id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_solutionTitle" value="<?php echo CHtml::encode($custom->solution_title); ?>" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Solution"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[solution]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckEditForm_<?php echo $custom->id; ?>_solution" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($custom->solution); ?></textarea>
                                            </td>
                                        </tr>
                                        <?php if (User::checkRole(User::ROLE_USER) || $tc->attachments): ?>
                                            <tr>
                                                <th>
                                                    <?php echo Yii::t("app", "Attachments"); ?>
                                                </th>
                                                <td class="text">
                                                    <div class="file-input" id="upload-custom-link-<?php echo $custom->id; ?>">
                                                        <a href="#attachment"><?php echo Yii::t("app", "New Attachment"); ?></a>
                                                        <input type="file" name="TargetCustomCheckAttachmentUploadForm[attachment]" data-id="<?php echo $custom->id; ?>" data-upload-url="<?php echo $this->createUrl("project/uploadcustomattachment", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $custom->id)); ?>">
                                                    </div>

                                                    <div class="upload-message hide" id="upload-custom-message-<?php echo $custom->id; ?>"><?php echo Yii::t("app", "Uploading..."); ?></div>

                                                    <table class="table attachment-list<?php if (!$custom->attachments) echo " hide"; ?>">
                                                        <tbody>
                                                            <?php if ($custom->attachments): ?>
                                                                <?php foreach ($custom->attachments as $attachment): ?>
                                                                    <tr data-path="<?php echo $attachment->path; ?>" data-control-url="<?php echo $this->createUrl("project/controlcustomattachment"); ?>">
                                                                        <td class="info">
                                                                            <span contenteditable="true" class="single-line title" onblur="$(this).siblings('input').val($(this).text());">
                                                                                <?php echo CHtml::encode($attachment->title); ?>
                                                                            </span>
                                                                            <input type="hidden" name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[attachmentTitles][]" data-path="<?php echo $attachment->path; ?>" value="<?php echo CHtml::encode($attachment->title); ?>">
                                                                        </td>
                                                                        <td class="actions">
                                                                            <a href="<?php echo $this->createUrl("project/customattachment", array("path" => $attachment->path)); ?>" title="<?php echo Yii::t("app", "Download"); ?>"><i class="icon icon-download"></i></a>
                                                                            <a href="#del" title="<?php echo Yii::t("app", "Delete"); ?>" onclick="user.check.delCustomAttachment('<?php echo $attachment->path; ?>');"><i class="icon icon-remove"></i></a>
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
                                                <ul class="rating">
                                                    <?php foreach (TargetCustomCheck::getValidRatings() as $rating): ?>
                                                        <li>
                                                            <label class="radio">
                                                                <input type="radio" name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[rating]" value="<?php echo $rating; ?>" <?php if ($custom->rating == $rating) echo "checked"; ?> <?php if (User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
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
                                                <input type="checkbox" name="TargetCustomCheckEditForm_<?php echo $custom->id; ?>[createCheck]" value="1" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                            </td>
                                        </tr>

                                        <?php if (User::checkRole(User::ROLE_USER)): ?>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>
                                                    <button class="btn" onclick="user.check.saveCustom(<?php echo $custom->id; ?>);"><?php echo Yii::t("app", "Save"); ?></button>&nbsp;
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
                echo $this->renderPartial("partial/right-block", array(
                    "quickTargets" => $quickTargets,
                    "project" => $project,
                    "client" => $client,
                    "statuses" => $statuses,
                    "category" => $category,
                    "target" => $target
                ));
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

                $ratingNames[] = $k . ":" . json_encode(array(
                    "text" => CHtml::encode($v),
                    "classN" => $class
                ));
            }

            echo implode(",", $ratingNames);
        ?>
    };

<?php if (User::checkRole(User::ROLE_USER)): ?>
    $(function () {
        user.check.initTargetCustomCheckAttachmentUploadForms();
        user.check.runningChecks = [
            <?php
                $runningChecks = array();

                foreach ($controls as $control) {
                    foreach ($control->checks as $check) {
                        foreach ($check->targetChecks as $tc) {
                            if ($tc->isRunning) {
                                $started = TargetCheckManager::getStartTime($tc->id);
                                $runningChecks[] = json_encode(array(
                                    "id" => $tc->id,
                                    "time" => $started != NULL ? time() - strtotime($started) : -1,
                                ));
                            }
                        }
                    }
                }

                echo implode(",", $runningChecks);
            ?>
        ];

        setTimeout(function () {
            user.check.update("<?php echo $this->createUrl("project/updatechecks", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id)); ?>");
        }, 1000);

        $(".wysiwyg").ckeditor();
    });
<?php endif; ?>
</script>