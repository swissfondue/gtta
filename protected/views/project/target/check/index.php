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

            foreach ($checks as $check)
                if ($check->automated) {
                    $hasAutomated = true;
                    break;
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
                            <div id="custom-check-<?php echo $check->control->id; ?>" class="check-header" data-id="custom-<?php echo $check->control->id; ?>">
                                <table class="check-header">
                                    <tbody>
                                        <tr>
                                            <td class="name">
                                                <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                    <a href="#custom-check-<?php echo $check->control->id; ?>" onclick="user.check.toggle(<?php echo $check->control->id; ?>, true);"><?php echo Yii::t("app", "Custom Check"); ?></a>
                                                <?php else: ?>
                                                    <a href="#custom-check-<?php echo $check->control->id; ?>" onclick="client.check.toggle(<?php echo $check->control->id; ?>, true);"><?php echo Yii::t("app", "Custom Check"); ?></a>
                                                <?php endif; ?>
                                            </td>
                                            <td class="status">
                                                <?php if ($check->control->customCheck): ?>
                                                    <?php
                                                        switch ($check->control->customCheck[0]->rating) {
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
                                                                echo "<span class=\"label\">" . $ratings[$check->control->customCheck[0]->rating] . "</span>";
                                                                break;
                                                        }
                                                    ?>
                                                <?php else: ?>
                                                    &nbsp;
                                                <?php endif; ?>
                                            </td>
                                            <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                <td class="actions">
                                                    <?php if ($check->control->customCheck): ?>
                                                        <a href="#reset" title="<?php echo Yii::t("app", "Reset"); ?>" onclick="user.check.reset(<?php echo $check->control->id; ?>, true);"><i class="icon icon-refresh"></i></a>
                                                    <?php else: ?>
                                                        <span class="disabled"><i class="icon icon-refresh" title="<?php echo Yii::t("app", "Reset"); ?>"></i></span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="check-form hide" data-id="custom-<?php echo $check->control->id; ?>" data-save-url="<?php echo $this->createUrl("project/savecustomcheck", array("id" => $project->id, "target" => $target->id)); ?>">
                                <table class="table check-form">
                                    <tbody>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Reference"); ?>
                                            </th>
                                            <td class="text">
                                                <?php
                                                    echo $check->control->customCheck ? "CUSTOM-CHECK-" . $check->control->customCheck[0]->reference : "CUSTOM-CHECK";
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Name"); ?>
                                            </th>
                                            <td>
                                                <input type="text" class="input-xlarge" name="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>[name]" id="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>_name" value="<?php if ($check->control->customCheck) echo CHtml::encode($check->control->customCheck[0]->name); ?>" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Background Info"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>[backgroundInfo]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>_backgroundInfo" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php if ($check->control->customCheck) echo CHtml::encode($check->control->customCheck[0]->background_info); ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Question"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>[question]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>_question" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php if ($check->control->customCheck) echo CHtml::encode($check->control->customCheck[0]->question); ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Result"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>[result]" class="max-width" rows="10" id="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>_result" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php if ($check->control->customCheck) echo CHtml::encode($check->control->customCheck[0]->result); ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Solution Title"); ?>
                                            </th>
                                            <td>
                                                <input type="text" class="input-xlarge" name="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>[solutionTitle]" id="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>_solutionTitle" value="<?php if ($check->control->customCheck) echo CHtml::encode($check->control->customCheck[0]->solution_title); ?>" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Solution"); ?>
                                            </th>
                                            <td>
                                                <textarea name="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>[solution]" class="max-width wysiwyg" rows="10" id="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>_solution" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php if ($check->control->customCheck) echo CHtml::encode($check->control->customCheck[0]->solution); ?></textarea>
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
                                                                <input type="radio" name="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>[rating]" value="<?php echo $rating; ?>" <?php if (($check->control->customCheck && $check->control->customCheck[0]->rating == $rating) || ($rating == TargetCheck::RATING_NONE && !$check->control->customCheck)) echo "checked"; ?> <?php if (User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
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
                                                <input type="checkbox" name="TargetCustomCheckEditForm_<?php echo $check->control->id; ?>[createCheck]" value="1" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                            </td>
                                        </tr>

                                        <?php if (User::checkRole(User::ROLE_USER)): ?>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>
                                                    <button class="btn" onclick="user.check.saveCustom(<?php echo $check->control->id; ?>);"><?php echo Yii::t("app", "Save"); ?></button>&nbsp;
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                    <?php
                        endif;

                        $prevControl = $check->control->id;
                    ?>
                    <div id="check-<?php echo $check->id; ?>" class="check-header <?php if ($check->isRunning) echo "in-progress"; ?>" data-id="<?php echo $check->id; ?>" data-control-url="<?php echo $this->createUrl("project/controlcheck", array( "id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $check->id )); ?>" data-type="<?php echo $check->automated ? "automated" : "manual"; ?>" data-limited="<?php echo $limited ? 1 : 0; ?>">
                        <table class="check-header">
                            <tbody>
                                <tr>
                                    <td class="name">
                                        <?php if (User::checkRole(User::ROLE_USER)): ?>
                                            <a href="#check-<?php echo $check->id; ?>" onclick="user.check.toggle(<?php echo $check->id; ?>);"><?php echo CHtml::encode($check->localizedName); ?></a>
                                        <?php else: ?>
                                            <a href="#check-<?php echo $check->id; ?>" onclick="client.check.toggle(<?php echo $check->id; ?>);"><?php echo CHtml::encode($check->localizedName); ?></a>
                                        <?php endif; ?>

                                        <?php if ($check->automated && User::checkRole(User::ROLE_USER)): ?>
                                            <i class="icon-cog" title="<?php echo Yii::t("app", "Automated"); ?>"></i>
                                        <?php endif; ?>

                                        <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                            <a href="<?php echo $this->createUrl("check/editcheck", array( "id" => $check->control->check_category_id, "control" => $check->check_control_id, "check" => $check->id )); ?>"><i class="icon-edit" title="<?php echo Yii::t("app", "Edit"); ?>"></i></a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="status">
                                        <?php if (!$limited && $check->targetChecks && $check->targetChecks[0]->status == TargetCheck::STATUS_FINISHED): ?>
                                            <?php
                                                switch ($check->targetChecks[0]->rating) {
                                                    case TargetCheck::RATING_INFO:
                                                        echo "<span class=\"label label-info\">" . $ratings[TargetCheck::RATING_INFO] . "</span>";
                                                        break;

                                                    case TargetChecK::RATING_LOW_RISK:
                                                        echo "<span class=\"label label-low-risk\">" . $ratings[TargetCheck::RATING_LOW_RISK] . "</span>";
                                                        break;

                                                    case TargetChecK::RATING_MED_RISK:
                                                        echo "<span class=\"label label-med-risk\">" . $ratings[TargetCheck::RATING_MED_RISK] . "</span>";
                                                        break;

                                                    case TargetChecK::RATING_HIGH_RISK:
                                                        echo "<span class=\"label label-high-risk\">" . $ratings[TargetCheck::RATING_HIGH_RISK] . "</span>";
                                                        break;

                                                    default:
                                                        echo "<span class=\"label\">" . $ratings[$check->targetChecks[0]->rating] . "</span>";
                                                        break;
                                                }
                                            ?>
                                        <?php elseif ($check->isRunning): ?>
                                            <?php
                                                $seconds = $check->targetChecks[0]->started;

                                                if ($seconds)
                                                {
                                                    $seconds = time() - strtotime($seconds);
                                                    $minutes = 0;

                                                    if ($seconds > 59)
                                                    {
                                                        $minutes = floor($seconds / 60);
                                                        $seconds = $seconds - ($minutes * 60);
                                                    }

                                                    printf("%02d:%02d", $minutes, $seconds);
                                                }
                                                else
                                                    echo "00:00";
                                            ?>
                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </td>
                                    <?php if (User::checkRole(User::ROLE_USER)): ?>
                                        <td class="actions">
                                            <?php if ($check->automated && !$limited): ?>
                                                <?php if (!$check->targetChecks || $check->targetChecks && in_array($check->targetChecks[0]->status, array( TargetCheck::STATUS_OPEN, TargetCheck::STATUS_FINISHED ))): ?>
                                                    <a href="#start" title="<?php echo Yii::t("app", "Start"); ?>" onclick="user.check.start(<?php echo $check->id; ?>);"><i class="icon icon-play"></i></a>
                                                <?php elseif ($check->targetChecks && $check->targetChecks[0]->status == TargetCheck::STATUS_IN_PROGRESS): ?>
                                                    <a href="#stop" title="<?php echo Yii::t("app", "Stop"); ?>" onclick="user.check.stop(<?php echo $check->id; ?>);"><i class="icon icon-stop"></i></a>
                                                <?php else: ?>
                                                    <span class="disabled"><i class="icon icon-stop" title="<?php echo Yii::t("app", "Stop"); ?>"></i></span>
                                                <?php endif; ?>
                                                &nbsp;
                                            <?php endif; ?>

                                            <?php if (!$limited && $check->targetChecks && in_array($check->targetChecks[0]->status, array( TargetCheck::STATUS_OPEN, TargetCheck::STATUS_FINISHED ))): ?>
                                                <a href="#reset" title="<?php echo Yii::t("app", "Reset"); ?>" onclick="user.check.reset(<?php echo $check->id; ?>);"><i class="icon icon-refresh"></i></a>
                                            <?php else: ?>
                                                <span class="disabled"><i class="icon icon-refresh" title="<?php echo Yii::t("app", "Reset"); ?>"></i></span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="check-form hide" data-id="<?php echo $check->id; ?>" data-save-url="<?php echo $this->createUrl("project/savecheck", array( "id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $check->id )); ?>" data-autosave-url="<?php echo $this->createUrl("project/autosavecheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $check->id)); ?>">
                        <?php if ($limited): ?>
                            <?php echo Yii::t("app", "This check is not available in the demo version."); ?>
                        <?php else: ?>
                            <table class="table check-form">
                                <tbody>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Reference"); ?>
                                        </th>
                                        <td class="text">
                                            <?php
                                                $reference    = $check->_reference->name . ( $check->reference_code ? "-" . $check->reference_code : "" );
                                                $referenceUrl = "";

                                                if ($check->reference_code && $check->reference_url)
                                                    $referenceUrl = $check->reference_url;
                                                else if ($check->_reference->url)
                                                    $referenceUrl = $check->_reference->url;

                                                if ($referenceUrl)
                                                    $reference = "<a href=\"" . $referenceUrl . "\" target=\"_blank\">" . CHtml::encode($reference) . "</a>";
                                                else
                                                    $reference = CHtml::encode($reference);

                                                echo $reference;
                                            ?>
                                        </td>
                                    </tr>
                                    <?php if ($check->localizedBackgroundInfo): ?>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Background Info"); ?>
                                            </th>
                                            <td class="text">
                                                <div class="limiter"><?php echo $check->localizedBackgroundInfo; ?></div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if ($check->localizedHints): ?>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Hints"); ?>
                                            </th>
                                            <td class="text">
                                                <div class="limiter"><?php echo $check->localizedHints; ?></div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if ($check->localizedQuestion): ?>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Question"); ?>
                                            </th>
                                            <td class="text">
                                                <div class="limiter"><?php echo $check->localizedQuestion; ?></div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if ($check->automated): ?>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Override Target"); ?>
                                            </th>
                                            <td>
                                                <input type="text" class="input-xlarge" name="TargetCheckEditForm_<?php echo $check->id; ?>[overrideTarget]" id="TargetCheckEditForm_<?php echo $check->id; ?>_overrideTarget" value="<?php if ($check->targetChecks) echo CHtml::encode($check->targetChecks[0]->override_target); ?>" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                            </td>
                                        </tr>
                                        <?php if ($check->protocol): ?>
                                            <tr>
                                                <th>
                                                    <?php echo Yii::t("app", "Protocol"); ?>
                                                </th>
                                                <td>
                                                    <input type="text" class="input-xlarge" name="TargetCheckEditForm_<?php echo $check->id; ?>[protocol]" id="TargetCheckEditForm_<?php echo $check->id; ?>_protocol" value="<?php echo CHtml::encode($check->targetChecks ? $check->targetChecks[0]->protocol : $check->protocol); ?>" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($check->port): ?>
                                            <tr>
                                                <th>
                                                    <?php echo Yii::t("app", "Port"); ?>
                                                </th>
                                                <td>
                                                    <input type="text" class="input-xlarge" name="TargetCheckEditForm_<?php echo $check->id; ?>[port]" id="TargetCheckEditForm_<?php echo $check->id; ?>_port" value="<?php echo $check->targetChecks ? $check->targetChecks[0]->port : $check->port; ?>" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($check->scripts && $check->automated && User::checkRole(User::ROLE_USER)): ?>
                                        <?php foreach ($check->scripts as $script): ?>
                                            <?php
                                                if (!$script->inputs) {
                                                    continue;
                                                }
                                            ?>
                                            <?php if (count($check->scripts) > 1): ?>
                                                <tr class="script-inputs">
                                                    <th>
                                                        <?php echo CHtml::encode($script->package->name); ?>
                                                    </th>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php
                                                $groups = array();
                                                $group = array();

                                                if (count($script->inputs) > Yii::app()->params["maxCheckboxes"]) {
                                                    foreach ($script->inputs as $input) {
                                                        if (!in_array($input->type, array(CheckInput::TYPE_CHECKBOX, CheckInput::TYPE_FILE))) {
                                                            if (count($group) > Yii::app()->params["maxCheckboxes"]) {
                                                                $groups[] = $group;
                                                            }

                                                            $group = array();
                                                            continue;
                                                        }

                                                        $group[] = $input->id;
                                                    }
                                                }

                                                if (count($group) > Yii::app()->params["maxCheckboxes"]) {
                                                    $groups[] = $group;
                                                }
                                            ?>
                                            <?php foreach ($script->inputs as $input): ?>
                                                <?php
                                                    $currentGroup = false;
                                                    $position = false;

                                                    foreach ($groups as $group) {
                                                        $position = array_search($input->id, $group);

                                                        if ($position !== false) {
                                                            $currentGroup = $group;
                                                            break;
                                                        }
                                                    }

                                                    if ($currentGroup === false || $position === 0):
                                                ?>
                                                    <tr>
                                                        <th>
                                                            <?php if ($currentGroup === false): ?>
                                                                <?php echo CHtml::encode($input->localizedName); ?>
                                                            <?php else: ?>
                                                                <?php echo Yii::t("app", "Input Group"); ?>
                                                            <?php endif; ?>
                                                        </th>
                                                        <td>
                                                <?php endif; ?>

                                                <?php if ($input->type == CheckInput::TYPE_TEXT): ?>
                                                    <?php
                                                        $value = "";

                                                        if ($input->targetInputs)
                                                            foreach ($input->targetInputs as $inputValue)
                                                                if ($inputValue->check_input_id == $input->id)
                                                                {
                                                                    $value = $inputValue->value;
                                                                    break;
                                                                }

                                                        if ($value == NULL && $input->value != NULL)
                                                            $value = $input->value;

                                                        if ($value != NULL)
                                                            $value = CHtml::encode($value);
                                                    ?>
                                                    <input type="text" name="TargetCheckEditForm_<?php echo $check->id; ?>[inputs][<?php echo $input->id; ?>]" class="max-width" id="TargetCheckEditForm_<?php echo $check->id; ?>_inputs_<?php echo $input->id; ?>" <?php if ($check->isRunning) echo "readonly"; ?> value="<?php echo $value; ?>">
                                                <?php elseif ($input->type == CheckInput::TYPE_TEXTAREA): ?>
                                                    <?php
                                                        $value = "";

                                                        if ($input->targetInputs)
                                                            foreach ($input->targetInputs as $inputValue)
                                                                if ($inputValue->check_input_id == $input->id)
                                                                {
                                                                    $value = $inputValue->value;
                                                                    break;
                                                                }

                                                        if ($value == NULL && $input->value != NULL)
                                                            $value = $input->value;

                                                        if ($value != NULL)
                                                            $value = CHtml::encode($value);
                                                    ?>
                                                    <textarea wrap="off" name="TargetCheckEditForm_<?php echo $check->id; ?>[inputs][<?php echo $input->id; ?>]" class="max-width" rows="2" id="TargetCheckEditForm_<?php echo $check->id; ?>_inputs_<?php echo $input->id; ?>" <?php if ($check->isRunning) echo "readonly"; ?>><?php echo $value; ?></textarea>
                                                <?php elseif (in_array($input->type, array(CheckInput::TYPE_CHECKBOX, CheckInput::TYPE_FILE))): ?>
                                                    <?php
                                                        $value = "";

                                                        if ($input->targetInputs) {
                                                            foreach ($input->targetInputs as $inputValue) {
                                                                if ($inputValue->check_input_id == $input->id) {
                                                                    $value = $inputValue->value;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                    <?php if ($currentGroup !== false): ?>
                                                        <div class="input-group">
                                                            <label>
                                                    <?php endif; ?>

                                                    <input type="checkbox" name="TargetCheckEditForm_<?php echo $check->id; ?>[inputs][<?php echo $input->id; ?>]" id="TargetCheckEditForm_<?php echo $check->id; ?>_inputs_<?php echo $input->id; ?>" <?php if ($check->isRunning) echo "readonly"; ?> value="1"<?php if ($value) echo " checked"; ?>>

                                                    <?php if ($currentGroup !== false): ?>
                                                                <?php echo CHtml::encode($input->localizedName); ?>
                                                            </label>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php elseif ($input->type == CheckInput::TYPE_RADIO): ?>
                                                    <?php
                                                        $value = "";

                                                        if ($input->targetInputs) {
                                                            foreach ($input->targetInputs as $inputValue) {
                                                                if ($inputValue->check_input_id == $input->id) {
                                                                    $value = $inputValue->value;
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                        $radioBoxes = explode("\n", str_replace("\r", "", $input->value));
                                                    ?>

                                                    <ul class="radio-input">
                                                        <?php foreach ($radioBoxes as $radio): ?>
                                                            <li>
                                                                <label class="radio">
                                                                    <input name="TargetCheckEditForm_<?php echo $check->id; ?>[inputs][<?php echo $input->id; ?>]" type="radio" value="<?php echo CHtml::encode($radio); ?>" <?php if ($check->isRunning) echo "disabled"; ?> <?php if ($value == $radio) echo " checked"; ?>>
                                                                    <?php echo CHtml::encode($radio); ?>
                                                                </label>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>

                                                <?php if ($input->localizedDescription && $currentGroup === false): ?>
                                                    <p class="help-block">
                                                        <?php echo CHtml::encode($input->localizedDescription); ?>
                                                    </p>
                                                <?php endif; ?>

                                                <?php if ($currentGroup === false || $position === count($currentGroup) - 1): ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Result"); ?>
                                        </th>
                                        <td>
                                            <textarea name="TargetCheckEditForm_<?php echo $check->id; ?>[result]" class="max-width result" rows="10" id="TargetCheckEditForm_<?php echo $check->id; ?>_result" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php if ($check->targetChecks) echo $check->targetChecks[0]->result; ?></textarea>

                                            <?php
                                                $showAuto = false;

                                                if ($check->automated && $check->targetChecks && $check->targetChecks[0]->started && $check->targetChecks[0]->status == TargetCheck::STATUS_IN_PROGRESS) {
                                                    $showAuto = true;
                                                }
                                            ?>

                                            <div class="automated-info-block <?php if (!$showAuto) echo "hide"; ?>">
                                                <?php
                                                    if ($showAuto) {
                                                        $started = new DateTime($check->targetChecks[0]->started);
                                                        $user = $check->targetChecks[0]->user;

                                                        echo Yii::t("app", "Started by {user} on {date} at {time}", array(
                                                            "{user}" => $user->name ? $user->name : $user->email,
                                                            "{date}" => $started->format("d.m.Y"),
                                                            "{time}" => $started->format("H:i:s"),
                                                        ));
                                                    }
                                                ?>
                                            </div>

                                            <div class="table-result">
                                                <?php
                                                    if ($check->targetChecks && $check->targetChecks[0]->table_result)
                                                    {
                                                        $table = new ResultTable();
                                                        $table->parse($check->targetChecks[0]->table_result);
                                                        echo $this->renderPartial("/project/target/check/tableresult", array( "table" => $table ));
                                                    }
                                                ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php if ($check->results && User::checkRole(User::ROLE_USER)): ?>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Insert Result"); ?>
                                            </th>
                                            <td class="text">
                                                <ul class="results">
                                                    <?php foreach ($check->results as $result): ?>
                                                        <li>
                                                            <div class="result-header">
                                                                <a href="#insert" onclick="user.check.insertResult(<?php echo $check->id; ?>, $('.result-content[data-id=<?php echo $result->id; ?>]').html());"><?php echo CHtml::encode($result->localizedTitle); ?></a>

                                                                <span class="result-control" data-id="<?php echo $result->id; ?>">
                                                                    <a href="#result" onclick="user.check.expandResult(<?php echo $result->id; ?>);"><i class="icon-chevron-down"></i></a>
                                                                </span>
                                                            </div>

                                                            <div class="result-content hide" data-id="<?php echo $result->id; ?>"><?php echo str_replace("\n", "<br>", CHtml::encode($result->localizedResult)); ?></div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t("app", "Solution"); ?>
                                        </th>
                                        <td class="text">
                                            <ul class="solutions">
                                                <?php if (!$check->multiple_solutions): ?>
                                                    <li>
                                                        <div class="solution-header">
                                                            <label class="radio">
                                                                <input name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="radio" value="0" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?> <?php if (!$check->targetCheckSolutions) echo "checked"; ?>>
                                                                <?php echo Yii::t("app", "None"); ?>
                                                            </label>
                                                        </div>
                                                    </li>
                                                <?php endif; ?>

                                                <li>
                                                    <div class="solution-header">
                                                        <?php if ($check->multiple_solutions): ?>
                                                            <label class="checkbox">
                                                                <input class="custom-solution" name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="checkbox" value="<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>" <?php if ($check->targetChecks && $check->targetChecks[0]->solution) echo "checked"; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                                        <?php else: ?>
                                                            <label class="radio">
                                                                <input class="custom-solution" name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="radio" value="<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>" <?php if ($check->targetChecks && $check->targetChecks[0]->solution) echo "checked"; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                                        <?php endif; ?>
                                                            <?php echo Yii::t("app", "Custom Solution"); ?>

                                                            <span class="solution-control" data-id="<?php echo $check->id; ?>-<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>">
                                                                <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                                    <a href="#solution" onclick="user.check.expandSolution('<?php echo $check->id; ?>-<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>');"><i class="icon-chevron-down"></i></a>
                                                                <?php else: ?>
                                                                    <a href="#solution" onclick="client.check.expandSolution('<?php echo $check->id; ?>-<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>');"><i class="icon-chevron-down"></i></a>
                                                                <?php endif; ?>
                                                            </span>
                                                        </label>
                                                    </div>

                                                    <div class="solution-content hide" data-id="<?php echo $check->id; ?>-<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>">
                                                        <input type="text" name="TargetCheckEditForm_<?php echo $check->id; ?>[solutionTitle]" class="max-width" id="TargetCheckEditForm_<?php echo $check->id; ?>_solutionTitle" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?> value="<?php if ($check->targetChecks) echo CHtml::encode($check->targetChecks[0]->solution_title); ?>">
                                                        <textarea name="TargetCheckEditForm_<?php echo $check->id; ?>[solution]" class="solution-edit wysiwyg max-width result" rows="10" id="TargetCheckEditForm_<?php echo $check->id; ?>_solution" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php if ($check->targetChecks) echo CHtml::encode($check->targetChecks[0]->solution); ?></textarea>

                                                        <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                                            <label class="checkbox">
                                                                <input name="TargetCheckEditForm_<?php echo $check->id; ?>[saveSolution]" type="checkbox" value="1" <?php if ($check->isRunning) echo "disabled"; ?>>
                                                                <?php echo Yii::t("app", "Save As Generic"); ?>
                                                            </label>
                                                        <?php endif; ?>
                                                    </div>
                                                </li>

                                                <?php foreach ($check->solutions as $solution): ?>
                                                    <li>
                                                        <div class="solution-header">
                                                            <?php
                                                                $checked = false;

                                                                if ($check->targetCheckSolutions)
                                                                    foreach ($check->targetCheckSolutions as $solutionValue)
                                                                        if ($solutionValue->check_solution_id == $solution->id)
                                                                        {
                                                                            $checked = true;
                                                                            break;
                                                                        }
                                                            ?>
                                                            <?php if ($check->multiple_solutions): ?>
                                                                <label class="checkbox">
                                                                    <input name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="checkbox" value="<?php echo $solution->id; ?>" <?php if ($checked) echo "checked"; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                                            <?php else: ?>
                                                                <label class="radio">
                                                                    <input name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="radio" value="<?php echo $solution->id; ?>" <?php if ($checked) echo "checked"; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                                            <?php endif; ?>
                                                                <?php echo CHtml::encode($solution->localizedTitle); ?>

                                                                <span class="solution-control" data-id="<?php echo $solution->id; ?>">
                                                                    <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                                        <a href="#solution" onclick="user.check.expandSolution(<?php echo $solution->id; ?>);"><i class="icon-chevron-down"></i></a>
                                                                    <?php else: ?>
                                                                        <a href="#solution" onclick="client.check.expandSolution(<?php echo $solution->id; ?>);"><i class="icon-chevron-down"></i></a>
                                                                    <?php endif; ?>
                                                                </span>
                                                            </label>
                                                        </div>

                                                        <div class="solution-content hide" data-id="<?php echo $solution->id; ?>">
                                                            <?php echo $solution->localizedSolution; ?>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
                                    <?php if (User::checkRole(User::ROLE_USER) || $check->targetCheckAttachments): ?>
                                        <tr>
                                            <th>
                                                <?php echo Yii::t("app", "Attachments"); ?>
                                            </th>
                                            <td class="text">
                                                <div class="file-input" id="upload-link-<?php echo $check->id; ?>">
                                                    <a href="#attachment"><?php echo Yii::t("app", "New Attachment"); ?></a>
                                                    <input type="file" name="TargetCheckAttachmentUploadForm[attachment]" data-id="<?php echo $check->id; ?>" data-upload-url="<?php echo $this->createUrl("project/uploadattachment", array( "id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $check->id )); ?>">
                                                </div>

                                                <div class="upload-message hide" id="upload-message-<?php echo $check->id; ?>"><?php echo Yii::t("app", "Uploading..."); ?></div>

                                                <table class="table attachment-list<?php if (!$check->targetCheckAttachments) echo " hide"; ?>">
                                                    <tbody>
                                                        <?php if ($check->targetCheckAttachments): ?>
                                                            <?php foreach ($check->targetCheckAttachments as $attachment): ?>
                                                                <tr data-path="<?php echo $attachment->path; ?>" data-control-url="<?php echo $this->createUrl("project/controlattachment"); ?>">
                                                                    <td class="name">
                                                                        <a href="<?php echo $this->createUrl("project/attachment", array( "path" => $attachment->path )); ?>"><?php echo CHtml::encode($attachment->name); ?></a>
                                                                    </td>
                                                                    <td class="actions">
                                                                        <a href="#del" title="<?php echo Yii::t("app", "Delete"); ?>" onclick="user.check.delAttachment('<?php echo $attachment->path; ?>');"><i class="icon icon-remove"></i></a>
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
                                                <?php foreach (TargetCheck::getValidRatings() as $rating): ?>
                                                    <li>
                                                        <label class="radio">
                                                            <input type="radio" name="TargetCheckEditForm_<?php echo $check->id; ?>[rating]" value="<?php echo $rating; ?>" <?php if (($check->targetChecks && $check->targetChecks[0]->rating == $rating) || ($rating == TargetCheck::RATING_NONE && (!$check->targetChecks || !$check->targetChecks[0]->rating))) echo "checked"; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                                            <?php echo $ratings[$rating]; ?>
                                                        </label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
                                    <?php if (User::checkRole(User::ROLE_USER)): ?>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                <button class="btn" onclick="user.check.save(<?php echo $check->id; ?>, false);" <?php if ($check->isRunning) echo "disabled"; ?>><?php echo Yii::t("app", "Save"); ?></button>&nbsp;
                                                <?php if ($counter < count($checks) - 1): ?>
                                                    <button class="btn" onclick="user.check.save(<?php echo $check->id; ?>, true);" <?php if ($check->isRunning) echo "disabled"; ?>><?php echo Yii::t("app", "Save & Next"); ?></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
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

                foreach ($checks as $check)
                    if ($check->isRunning)
                    {
                        $runningChecks[] = json_encode(array(
                            "id" => $check->id,
                            "time" => $check->targetChecks[0]->started != NULL ? time() - strtotime($check->targetChecks[0]->started) : -1,
                        ));
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