<div id="check-<?php echo $tc->id; ?>" class="check-header <?php if ($tc->isRunning) echo "in-progress"; ?>" data-id="<?php echo $tc->id; ?>" data-control-url="<?php echo $this->createUrl("project/controlcheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $tc->id)); ?>" data-type="<?php echo $check->automated ? "automated" : "manual"; ?>" data-limited="<?php echo $limited ? 1 : 0; ?>">
    <table class="check-header <?php if ($limited) echo "limited"; ?>">
        <tbody>
            <tr>
                <td class="name">
                    <?php if (User::checkRole(User::ROLE_USER)): ?>
                        <a href="#check-<?php echo $tc->id; ?>" onclick="user.check.toggle(<?php echo $tc->id; ?>);"><?php echo CHtml::encode($check->localizedName); ?><?php if ($number > 0) echo " " . ($number + 1); ?></a>
                    <?php else: ?>
                        <a href="#check-<?php echo $tc->id; ?>" onclick="client.check.toggle(<?php echo $tc->id; ?>);"><?php echo CHtml::encode($check->localizedName); ?><?php if ($number > 0) echo " " . ($number + 1); ?></a>
                    <?php endif; ?>

                    <?php if ($check->automated && User::checkRole(User::ROLE_USER)): ?>
                        <i class="icon-cog" title="<?php echo Yii::t("app", "Automated"); ?>"></i>
                    <?php endif; ?>

                    <?php if (User::checkRole(User::ROLE_ADMIN) && !$limited): ?>
                        <a href="<?php echo $this->createUrl("check/editcheck", array("id" => $check->control->check_category_id, "control" => $check->check_control_id, "check" => $check->id)); ?>"><i class="icon-edit" title="<?php echo Yii::t("app", "Edit"); ?>"></i></a>
                    <?php endif; ?>

                    <?php if (User::checkRole(User::ROLE_USER) && $number == 0): ?>
                        <a href="#check-<?php echo $tc->id; ?>" onclick="user.check.copy(<?php echo $tc->id; ?>);"><i class="icon-plus" title="<?php echo Yii::t("app", "Copy"); ?>"></i></a>
                    <?php endif; ?>

                    <?php if (User::checkRole(User::ROLE_USER) && count($check->targetChecks) > 1): ?>
                        <a href="#check-<?php echo $tc->id; ?>" onclick="user.check.del(<?php echo $tc->id; ?>);"><i class="icon-remove" title="<?php echo Yii::t("app", "Delete"); ?>"></i></a>
                    <?php endif; ?>
                </td>
                <td class="status">
                    <?php if (!$limited && $tc->status == TargetCheck::STATUS_FINISHED): ?>
                        <?php
                            switch ($tc->rating) {
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
                                    echo "<span class=\"label\">" . $ratings[$tc->rating] . "</span>";
                                    break;
                            }
                        ?>
                    <?php elseif ($tc->isRunning): ?>
                        <?php
                            $seconds = $tc->started;

                            if ($seconds) {
                                $seconds = time() - strtotime($seconds);
                                $minutes = 0;

                                if ($seconds > 59) {
                                    $minutes = floor($seconds / 60);
                                    $seconds = $seconds - ($minutes * 60);
                                }

                                printf("%02d:%02d", $minutes, $seconds);
                            } else {
                                echo "00:00";
                            }
                        ?>
                    <?php else: ?>
                        &nbsp;
                    <?php endif; ?>
                </td>
                <?php if (User::checkRole(User::ROLE_USER)): ?>
                    <td class="actions">
                        <?php if ($check->automated && !$limited): ?>
                            <?php if (in_array($tc->status, array(TargetCheck::STATUS_OPEN, TargetCheck::STATUS_FINISHED))): ?>
                                <a href="#start" title="<?php echo Yii::t("app", "Start"); ?>" onclick="user.check.start(<?php echo $tc->id; ?>);"><i class="icon icon-play"></i></a>
                            <?php elseif ($tc->status == TargetCheck::STATUS_IN_PROGRESS): ?>
                                <a href="#stop" title="<?php echo Yii::t("app", "Stop"); ?>" onclick="user.check.stop(<?php echo $tc->id; ?>);"><i class="icon icon-stop"></i></a>
                            <?php else: ?>
                                <span class="disabled"><i class="icon icon-stop" title="<?php echo Yii::t("app", "Stop"); ?>"></i></span>
                            <?php endif; ?>
                            &nbsp;
                        <?php endif; ?>

                        <?php if (!$limited && in_array($tc->status, array(TargetCheck::STATUS_OPEN, TargetCheck::STATUS_FINISHED))): ?>
                            <a href="#reset" title="<?php echo Yii::t("app", "Reset"); ?>" onclick="user.check.reset(<?php echo $tc->id; ?>);"><i class="icon icon-refresh"></i></a>
                        <?php else: ?>
                            <span class="disabled"><i class="icon icon-refresh" title="<?php echo Yii::t("app", "Reset"); ?>"></i></span>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>
            </tr>
        </tbody>
    </table>
</div>
<div class="check-form hide" data-id="<?php echo $tc->id; ?>" data-save-url="<?php echo $this->createUrl("project/savecheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $tc->id)); ?>" data-autosave-url="<?php echo $this->createUrl("project/autosavecheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $tc->id)); ?>">
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
                            $reference = $check->_reference->name . ($check->reference_code ? "-" . $check->reference_code : "");
                            $referenceUrl = "";

                            if ($check->reference_code && $check->reference_url) {
                                $referenceUrl = $check->reference_url;
                            } else if ($check->_reference->url) {
                                $referenceUrl = $check->_reference->url;
                            }

                            if ($referenceUrl) {
                                $reference = "<a href=\"" . $referenceUrl . "\" target=\"_blank\">" . CHtml::encode($reference) . "</a>";
                            } else {
                                $reference = CHtml::encode($reference);
                            }

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
                            <input type="text" class="input-xlarge" name="TargetCheckEditForm_<?php echo $tc->id; ?>[overrideTarget]" id="TargetCheckEditForm_<?php echo $tc->id; ?>_overrideTarget" value="<?php echo CHtml::encode($tc->override_target); ?>" <?php if ($tc->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                        </td>
                    </tr>
                    <?php if ($check->protocol): ?>
                        <tr>
                            <th>
                                <?php echo Yii::t("app", "Protocol"); ?>
                            </th>
                            <td>
                                <input type="text" class="input-xlarge" name="TargetCheckEditForm_<?php echo $tc->id; ?>[protocol]" id="TargetCheckEditForm_<?php echo $tc->id; ?>_protocol" value="<?php echo CHtml::encode($tc->protocol); ?>" <?php if ($tc->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($check->port): ?>
                        <tr>
                            <th>
                                <?php echo Yii::t("app", "Port"); ?>
                            </th>
                            <td>
                                <input type="text" class="input-xlarge" name="TargetCheckEditForm_<?php echo $tc->id; ?>[port]" id="TargetCheckEditForm_<?php echo $tc->id; ?>_port" value="<?php echo $tc->port; ?>" <?php if ($tc->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
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
                                <input type="text" name="TargetCheckEditForm_<?php echo $tc->id; ?>[inputs][<?php echo $input->id; ?>]" class="max-width" id="TargetCheckEditForm_<?php echo $tc->id; ?>_inputs_<?php echo $input->id; ?>" <?php if ($tc->isRunning) echo "readonly"; ?> value="<?php echo $value; ?>">
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
                                <textarea wrap="off" name="TargetCheckEditForm_<?php echo $tc->id; ?>[inputs][<?php echo $input->id; ?>]" class="max-width" rows="2" id="TargetCheckEditForm_<?php echo $tc->id; ?>_inputs_<?php echo $input->id; ?>" <?php if ($tc->isRunning) echo "readonly"; ?>><?php echo $value; ?></textarea>
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

                                <input type="checkbox" name="TargetCheckEditForm_<?php echo $tc->id; ?>[inputs][<?php echo $input->id; ?>]" id="TargetCheckEditForm_<?php echo $tc->id; ?>_inputs_<?php echo $input->id; ?>" <?php if ($tc->isRunning) echo "readonly"; ?> value="1"<?php if ($value) echo " checked"; ?>>

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
                                                <input name="TargetCheckEditForm_<?php echo $tc->id; ?>[inputs][<?php echo $input->id; ?>]" type="radio" value="<?php echo CHtml::encode($radio); ?>" <?php if ($tc->isRunning) echo "disabled"; ?> <?php if ($value == $radio) echo " checked"; ?>>
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
                        <textarea name="TargetCheckEditForm_<?php echo $tc->id; ?>[result]" class="max-width result" rows="10" id="TargetCheckEditForm_<?php echo $tc->id; ?>_result" <?php if ($tc->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo $tc->result; ?></textarea>

                        <?php
                            $showAuto = false;

                            if ($check->automated && $tc->started && $tc->status == TargetCheck::STATUS_IN_PROGRESS) {
                                $showAuto = true;
                            }
                        ?>

                        <div class="automated-info-block <?php if (!$showAuto) echo "hide"; ?>">
                            <?php
                                if ($showAuto) {
                                    $started = new DateTime($tc->started);
                                    $user = $tc->user;

                                    echo Yii::t("app", "Started by {user} on {date} at {time}", array(
                                        "{user}" => $user->name ? $user->name : $user->email,
                                        "{date}" => $started->format("d.m.Y"),
                                        "{time}" => $started->format("H:i:s"),
                                    ));
                                }
                            ?>
                        </div>

                        <br>

                        <span class="help-block pull-right">
                            <a class="btn btn-default" href="#editor" onclick="user.check.toggleEditor('TargetCheckEditForm_<?php echo $tc->id; ?>_result');">
                                <span class="glyphicon glyphicon-edit"></span>
                                <?php echo Yii::t("app", "WYSIWYG"); ?>
                            </a>
                        </span>

                        <div class="table-result">
                            <?php
                                if ($tc->table_result) {
                                    $table = new ResultTable();
                                    $table->parse($tc->table_result);
                                    echo $this->renderPartial("/project/target/check/tableresult", array("table" => $table));
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
                                            <a href="#insert" onclick="user.check.insertResult(<?php echo $tc->id; ?>, $('.result-content[data-id=<?php echo $result->id; ?>]').html());"><?php echo CHtml::encode($result->localizedTitle); ?></a>

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
                                            <input name="TargetCheckEditForm_<?php echo $tc->id; ?>[solutions][]" type="radio" value="0" <?php if ($tc->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?> <?php if (!$tc->solutions) echo "checked"; ?>>
                                            <?php echo Yii::t("app", "None"); ?>
                                        </label>
                                    </div>
                                </li>
                            <?php endif; ?>

                            <li>
                                <div class="solution-header">
                                    <?php if ($check->multiple_solutions): ?>
                                        <label class="checkbox">
                                            <input class="custom-solution" name="TargetCheckEditForm_<?php echo $tc->id; ?>[solutions][]" type="checkbox" value="<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>" <?php if ($tc->solution) echo "checked"; ?> <?php if ($tc->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                    <?php else: ?>
                                        <label class="radio">
                                            <input class="custom-solution" name="TargetCheckEditForm_<?php echo $tc->id; ?>[solutions][]" type="radio" value="<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>" <?php if ($tc->solution) echo "checked"; ?> <?php if ($tc->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                    <?php endif; ?>
                                        <?php echo Yii::t("app", "Custom Solution"); ?>

                                        <span class="solution-control" data-id="<?php echo $tc->id; ?>-<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>">
                                            <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                <a href="#solution" onclick="user.check.expandSolution('<?php echo $tc->id; ?>-<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>');"><i class="icon-chevron-down"></i></a>
                                            <?php else: ?>
                                                <a href="#solution" onclick="client.check.expandSolution('<?php echo $tc->id; ?>-<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>');"><i class="icon-chevron-down"></i></a>
                                            <?php endif; ?>
                                        </span>
                                    </label>
                                </div>

                                <div class="solution-content hide" data-id="<?php echo $tc->id; ?>-<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>">
                                    <input type="text" name="TargetCheckEditForm_<?php echo $tc->id; ?>[solutionTitle]" class="max-width" id="TargetCheckEditForm_<?php echo $tc->id; ?>_solutionTitle" <?php if ($tc->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?> value="<?php echo CHtml::encode($tc->solution_title); ?>">
                                    <textarea name="TargetCheckEditForm_<?php echo $tc->id; ?>[solution]" class="solution-edit wysiwyg max-width result" rows="10" id="TargetCheckEditForm_<?php echo $tc->id; ?>_solution" <?php if ($tc->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($tc->solution); ?></textarea>

                                    <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                        <label class="checkbox">
                                            <input name="TargetCheckEditForm_<?php echo $tc->id; ?>[saveSolution]" type="checkbox" value="1" <?php if ($tc->isRunning) echo "disabled"; ?>>
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

                                            if ($tc->solutions) {
                                                foreach ($tc->solutions as $solutionValue) {
                                                    if ($solutionValue->check_solution_id == $solution->id) {
                                                        $checked = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                        <?php if ($check->multiple_solutions): ?>
                                            <label class="checkbox">
                                                <input name="TargetCheckEditForm_<?php echo $tc->id; ?>[solutions][]" type="checkbox" value="<?php echo $solution->id; ?>" <?php if ($checked) echo "checked"; ?> <?php if ($tc->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                                        <?php else: ?>
                                            <label class="radio">
                                                <input name="TargetCheckEditForm_<?php echo $tc->id; ?>[solutions][]" type="radio" value="<?php echo $solution->id; ?>" <?php if ($checked) echo "checked"; ?> <?php if ($tc->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
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
                <?php if (User::checkRole(User::ROLE_USER) || $tc->attachments): ?>
                    <tr>
                        <th>
                            <?php echo Yii::t("app", "Attachments"); ?>
                        </th>
                        <td class="text">
                            <div class="file-input" id="upload-link-<?php echo $tc->id; ?>">
                                <a href="#attachment"><?php echo Yii::t("app", "New Attachment"); ?></a>
                                <input type="file" name="TargetCheckAttachmentUploadForm[attachment]" data-id="<?php echo $tc->id; ?>" data-upload-url="<?php echo $this->createUrl("project/uploadattachment", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $tc->id)); ?>">
                            </div>

                            <div class="upload-message hide" id="upload-message-<?php echo $tc->id; ?>"><?php echo Yii::t("app", "Uploading..."); ?></div>

                            <table class="table attachment-list<?php if (!$tc->attachments) echo " hide"; ?>">
                                <tbody>
                                    <?php if ($tc->attachments): ?>
                                        <?php foreach ($tc->attachments as $attachment): ?>
                                            <tr data-path="<?php echo $attachment->path; ?>" data-control-url="<?php echo $this->createUrl("project/controlattachment"); ?>">
                                                <td class="name">
                                                    <a href="<?php echo $this->createUrl("project/attachment", array("path" => $attachment->path)); ?>"><?php echo CHtml::encode($attachment->name); ?></a>
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
                                        <input type="radio" name="TargetCheckEditForm_<?php echo $tc->id; ?>[rating]" value="<?php echo $rating; ?>" <?php if (($tc->rating == $rating) || ($rating == TargetCheck::RATING_NONE && !$tc->rating)) echo "checked"; ?> <?php if ($tc->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
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
                            <button class="btn" onclick="user.check.save(<?php echo $tc->id; ?>, false);" <?php if ($tc->isRunning) echo "disabled"; ?>><?php echo Yii::t("app", "Save"); ?></button>&nbsp;
                            <?php if (!$last): ?>
                                <button class="btn" onclick="user.check.save(<?php echo $tc->id; ?>, true);" <?php if ($tc->isRunning) echo "disabled"; ?>><?php echo Yii::t("app", "Save & Next"); ?></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>