<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.ui.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.iframe-transport.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.fileupload.js"></script>
<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>

<?php if (User::checkRole(User::ROLE_USER)): ?>
    <div class="pull-right buttons">
        <div class="btn-group" data-toggle="buttons-radio">
            <button class="btn <?php if (!$category->advanced) echo "active"; ?>" onclick="user.check.setAdvanced('<?php echo $this->createUrl("project/savecategory", array( "id" => $project->id, "target" => $target->id, "category" => $category->check_category_id )); ?>', 0);"><?php echo Yii::t("app", "Basic"); ?></button>
            <button class="btn <?php if ($category->advanced)  echo "active"; ?>" onclick="user.check.setAdvanced('<?php echo $this->createUrl("project/savecategory", array( "id" => $project->id, "target" => $target->id, "category" => $category->check_category_id )); ?>', 1);"><?php echo Yii::t("app", "Advanced"); ?></button>
        </div>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="#expand-all" onclick="user.check.expandAll();"><i class="icon icon-arrow-down"></i> <?php echo Yii::t("app", "Expand"); ?></a>&nbsp;
        <a class="btn" href="#collapse-all" onclick="user.check.collapseAll();"><i class="icon icon-arrow-up"></i> <?php echo Yii::t("app", "Collapse"); ?></a>&nbsp;

        <?php
            $hasAutomated = false;

            foreach ($checks as $check) {
                if ($check->automated) {
                    $hasAutomated = true;
                    break;
                }
            }

            if ($hasAutomated):
        ?>
            <a class="btn" href="#start-all" onclick="user.check.startAll();"><i class="icon icon-play"></i> <?php echo Yii::t("app", "Start"); ?></a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($checks) > 0): ?>
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
                <?php
                    $counter = 0;
                    $prevControl = 0;

                    $collapseControls = count($checks) >= Yii::app()->params["collapseCheckCount"];

                    foreach ($checks as $check):
                ?>
                    <?php
                        $limited = false;

                        if ($this->_system->demo && !$check->demo) {
                            $limited = true;
                        }
                    ?>

                    <?php if ($check->control->id != $prevControl): ?>
                        <?php if ($prevControl != 0): ?>
                            </div>
                        <?php endif; ?>

                        <div id="control-<?php echo $check->control->id; ?>" class="control-header" data-id="<?php echo $check->control->id; ?>">
                            <table class="table control-header">
                                <tbody>
                                    <tr>
                                        <td class="name">
                                            <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                <a href="#control-<?php echo $check->control->id; ?>" onclick="user.check.toggleControl(<?php echo $check->control->id; ?>);"><?php echo CHtml::encode($check->control->localizedName); ?></a>
                                            <?php else: ?>
                                                <a href="#control-<?php echo $check->control->id; ?>" onclick="client.check.toggleControl(<?php echo $check->control->id; ?>);"><?php echo CHtml::encode($check->control->localizedName); ?></a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="stats">
                                            <span class="high-risk"><?php echo $controlStats[$check->control->id]["highRisk"]; ?></span> /
                                            <span class="med-risk"><?php echo $controlStats[$check->control->id]["medRisk"]; ?></span> /
                                            <span class="low-risk"><?php echo $controlStats[$check->control->id]["lowRisk"]; ?></span> /
                                            <span class="info"><?php echo $controlStats[$check->control->id]["info"]; ?></span>
                                        </td>
                                        <td class="percent">
                                            <?php echo $controlStats[$check->control->id]["checks"] ? sprintf("%.0f", ($controlStats[$check->control->id]["finished"] / $controlStats[$check->control->id]["checks"]) * 100) : "0"; ?>% /
                                            <?php echo $controlStats[$check->control->id]["finished"]; ?>
                                        </td>
                                        <td class="check-count">
                                            <?php echo $controlStats[$check->control->id]["checks"]; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="control-body<?php if ($collapseControls) echo " hide"; ?>" data-id="<?php echo $check->control->id; ?>">
                            <div id="custom-template-<?php echo $check->control->id; ?>" class="check-header" data-id="custom-template-<?php echo $check->control->id; ?>" data-control-url="<?php echo $this->createUrl("project/controlcustomcheck", array("id" => $project->id, "target" => $target->id)); ?>">
                                <table class="check-header">
                                    <tbody>
                                        <tr>
                                            <td class="name">
                                                <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                    <a href="#custom-template-<?php echo $check->control->id; ?>" onclick="user.check.toggle('custom-template-<?php echo $check->control->id; ?>');"><?php echo Yii::t("app", "Custom Check"); ?></a>
                                                <?php else: ?>
                                                    <a href="#custom-template-<?php echo $check->control->id; ?>" onclick="client.check.toggle('custom-template-<?php echo $check->control->id; ?>');"><?php echo Yii::t("app", "Custom Check"); ?></a>
                                                <?php endif; ?>
                                            </td>
                                            <td class="status">
                                                &nbsp;
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="check-form hide" data-id="custom-template-<?php echo $check->control->id; ?>" data-save-url="<?php echo $this->createUrl("project/savecustomcheck", array("id" => $project->id, "target" => $target->id)); ?>">
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
                                                <input type="text" class="input-xlarge" name="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>[name]" id="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>_name" value="" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Background Info"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>[backgroundInfo]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>_backgroundInfo" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Question"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>[question]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>_question" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Result"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>[result]" class="max-width" rows="10" id="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>_result" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Solution Title"); ?>
                                            </th>
                                            <td>
                                                <input type="text" class="input-xlarge" name="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>[solutionTitle]" id="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>_solutionTitle" value="" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Solution"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>[solution]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>_solution" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>></textarea>
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
                                                                <input type="radio" name="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>[rating]" value="<?php echo $rating; ?>" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
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
                                                <input type="checkbox" name="TargetCustomCheckTemplateEditForm_<?php echo $check->control->id; ?>[createCheck]" value="1" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                            </td>
                                        </tr>

                                        <?php if (User::checkRole(User::ROLE_USER)): ?>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>
                                                    <button class="btn" onclick="user.check.saveCustomTemplate(<?php echo $check->control->id; ?>);"><?php echo Yii::t("app", "Save"); ?></button>&nbsp;
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php foreach ($check->control->customChecks as $custom): ?>
                                <div id="custom-<?php echo $custom->id; ?>" class="check-header" data-id="custom-<?php echo $custom->id; ?>" data-control-url="<?php echo $this->createUrl("project/controlcustomcheck", array("id" => $project->id, "target" => $target->id)); ?>">
                                    <table class="check-header">
                                        <tbody>
                                            <tr>
                                                <td class="name">
                                                    <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                        <a href="#custom-<?php echo $custom->id; ?>" onclick="user.check.toggle('custom-<?php echo $custom->id; ?>');"><?php echo $custom->name ? CHtml::encode($custom->name) : "CUSTOM-CHECK-" . $custom->reference; ?></a>
                                                        <a href="#delete" title="<?php echo Yii::t("app", "Delete"); ?>" onclick="user.check.deleteCustom(<?php echo $custom->id; ?>);"><i class="icon icon-remove"></i></a>
                                                    <?php else: ?>
                                                        <a href="#custom-<?php echo $custom->id; ?>" onclick="client.check.toggle('custom-<?php echo $custom->id; ?>');"><?php echo $custom->name ? CHtml::encode($custom->name) : "CUSTOM-CHECK-" . $custom->reference; ?></a>
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

                                <div class="check-form hide" data-id="custom-<?php echo $custom->id; ?>" data-save-url="<?php echo $this->createUrl("project/savecustomcheck", array("id" => $project->id, "target" => $target->id)); ?>">
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
                                                </td>
                                            </tr>
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
                    <?php
                        endif;

                        $prevControl = $check->control->id;
                    ?>

                    <?php
                        $number = 0;

                        foreach ($check->targetChecks as $tc) {
                            $last = ($counter >= count($checks) - 1) && ($number >= count($check->targetChecks) - 1);

                            echo $this->renderPartial("partial/check", array(
                                "project" => $project,
                                "target" => $target,
                                "category" => $category,
                                "check" => $check,
                                "tc" => $tc,
                                "number" => $number,
                                "limited" => $limited,
                                "ratings" => $ratings,
                                "last" => $last,
                            ));

                            $number++;
                        }
                    ?>
                <?php
                        $counter++;
                    endforeach;
                ?>
                </div>
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
        user.check.initTargetCheckAttachmentUploadForms();
        user.check.initAutosave();

        user.check.runningChecks = [
            <?php
                $runningChecks = array();

                foreach ($checks as $check) {
                    foreach ($check->targetChecks as $tc) {
                        if ($tc->isRunning) {
                            $runningChecks[] = json_encode(array(
                                "id" => $tc->id,
                                "time" => $tc->started != NULL ? time() - strtotime($tc->started) : -1,
                            ));
                        }
                    }
                }

                echo implode(",", $runningChecks);
            ?>
        ];

        setTimeout(function () {
            user.check.update("<?php echo $this->createUrl("project/updatechecks", array( "id" => $project->id, "target" => $target->id, "category" => $category->check_category_id )); ?>");
        }, 1000);

        var href = window.location.href;

        if (href.indexOf("#check-") >= 0) {
            var checkId = href.substring(href.indexOf("#check-") + 7, href.length);
            user.check.expand(parseInt(checkId), function () {
                location.href = "#check-" + checkId;
            });
        }

        $(".wysiwyg").ckeditor();
    });
<?php endif; ?>
</script>