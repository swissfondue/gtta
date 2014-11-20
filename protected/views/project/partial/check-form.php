<table class="table check-form">
    <tbody>
        <tr>
            <th>
                <?php echo Yii::t("app", "Reference"); ?>
            </th>
            <td class="text">
                <?php
                    $reference = $check->check->_reference->name . ($check->check->reference_code ? "-" . $check->check->reference_code : "");
                    $referenceUrl = "";

                    if ($check->check->reference_code && $check->check->reference_url) {
                        $referenceUrl = $check->check->reference_url;
                    } else if ($check->check->_reference->url) {
                        $referenceUrl = $check->check->_reference->url;
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
        <?php if ($check->check->localizedBackgroundInfo): ?>
            <tr>
                <th>
                    <?php echo Yii::t("app", "Background Info"); ?>
                </th>
                <td class="text">
                    <div class="limiter"><?php echo $check->check->localizedBackgroundInfo; ?></div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if ($check->check->localizedHints): ?>
            <tr>
                <th>
                    <?php echo Yii::t("app", "Hints"); ?>
                </th>
                <td class="text">
                    <div class="limiter"><?php echo $check->check->localizedHints; ?></div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if ($check->check->localizedQuestion): ?>
            <tr>
                <th>
                    <?php echo Yii::t("app", "Question"); ?>
                </th>
                <td class="text">
                    <div class="limiter"><?php echo $check->check->localizedQuestion; ?></div>
                </td>
            </tr>
        <?php endif; ?>
        <?php if ($check->check->automated): ?>
            <tr>
                <th>
                    <?php echo Yii::t("app", "Override Target"); ?>
                </th>
                <td>
                    <input type="text" class="input-xlarge" name="TargetCheckEditForm_<?php echo $check->id; ?>[overrideTarget]" id="TargetCheckEditForm_<?php echo $check->id; ?>_overrideTarget" value="<?php echo CHtml::encode($check->override_target); ?>" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                </td>
            </tr>
            <?php if ($check->check->protocol): ?>
                <tr>
                    <th>
                        <?php echo Yii::t("app", "Protocol"); ?>
                    </th>
                    <td>
                        <input type="text" class="input-xlarge" name="TargetCheckEditForm_<?php echo $check->id; ?>[protocol]" id="TargetCheckEditForm_<?php echo $check->id; ?>_protocol" value="<?php echo CHtml::encode($check->protocol); ?>" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ($check->check->port): ?>
                <tr>
                    <th>
                        <?php echo Yii::t("app", "Port"); ?>
                    </th>
                    <td>
                        <input type="text" class="input-xlarge" name="TargetCheckEditForm_<?php echo $check->id; ?>[port]" id="TargetCheckEditForm_<?php echo $check->id; ?>_port" value="<?php echo $check->port; ?>" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($check->check->scripts && $check->check->automated && User::checkRole(User::ROLE_USER)): ?>
            <?php
                $scriptsToStart = PgArrayManager::pgArrayDecode($check->scripts_to_start);
                $checkAll = empty($scriptsToStart);
            ?>
            <?php foreach ($check->check->scripts as $script): ?>
                <?php if (count($check->check->scripts) > 1): ?>
                    <tr class="script-inputs">
                        <th>
                            <input name="TargetCheckEditForm_<?php echo $check->id ?>[scriptsToStart][]" type="checkbox" value="<?php echo $script->id; ?>" <?php echo ($checkAll || in_array($script->id, $scriptsToStart) ? 'checked="checked"' : ''); ?> <?php if ($check->isRunning) echo "disabled"; ?> />
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

                            if ($input->targetInputs) {
                                foreach ($input->targetInputs as $inputValue) {
                                    if ($inputValue->check_input_id == $input->id) {
                                        $value = $inputValue->value;
                                        break;
                                    }
                                }
                            }

                            if ($value == NULL && $input->value != NULL) {
                                $value = $input->value;
                            }

                            if ($value != NULL) {
                                $value = CHtml::encode($value);
                            }
                        ?>
                        <input type="text" name="TargetCheckEditForm_<?php echo $check->id; ?>[inputs][<?php echo $input->id; ?>]" class="max-width" id="TargetCheckEditForm_<?php echo $check->id; ?>_inputs_<?php echo $input->id; ?>" <?php if ($check->isRunning) echo "readonly"; ?> value="<?php echo $value; ?>">
                    <?php elseif ($input->type == CheckInput::TYPE_TEXTAREA): ?>
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

                            if ($value == NULL && $input->value != NULL) {
                                $value = $input->value;
                            }

                            if ($value != NULL) {
                                $value = CHtml::encode($value);
                            }
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
                <textarea name="TargetCheckEditForm_<?php echo $check->id; ?>[result]" class="max-width result <?php echo ( Utils::isHtml($check->result) ? 'html_content' : '' ); ?>" rows="10" id="TargetCheckEditForm_<?php echo $check->id; ?>_result" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo $check->result; ?></textarea>

                <?php
                    $showAuto = false;

                    if ($check->check->automated && $check->started && $check->status == TargetCheck::STATUS_IN_PROGRESS) {
                        $showAuto = true;
                    }
                ?>

                <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                    <label class="checkbox">
                        <input name="TargetCheckEditForm_<?php echo $check->id; ?>[saveResult]" type="checkbox" value="1" <?php if ($check->isRunning) echo "disabled"; ?> onchange="user.check.toggleField('TargetCheckEditForm_<?php echo $check->id; ?>_resultTitle')">
                        <?php echo Yii::t("app", "Save As Generic"); ?>
                    </label>

                    <input type="text" name="TargetCheckEditForm_<?php echo $check->id; ?>[resultTitle]" class="max-width" style="display: none" id="TargetCheckEditForm_<?php echo $check->id; ?>_resultTitle" <?php if ($check->isRunning) echo "readonly"; ?> placeholder="<?php echo Yii::t("app", "Result Title"); ?>">
                <?php endif; ?>

                <div class="automated-info-block <?php if (!$showAuto) echo "hide"; ?>">
                    <?php
                        if ($showAuto) {
                            $started = new DateTime($check->started);
                            $user = $check->user;

                            echo Yii::t("app", "Started by {user} on {date} at {time}", array(
                                "{user}" => $user->name ? $user->name : $user->email,
                                "{date}" => $started->format("d.m.Y"),
                                "{time}" => $started->format("H:i:s"),
                            ));
                        }
                    ?>
                </div>

                <?php if (User::checkRole(User::ROLE_USER)): ?>
                    <br>

                    <span class="help-block pull-right">
                        <a class="btn btn-default" href="#editor" onclick="user.check.toggleEditor('TargetCheckEditForm_<?php echo $check->id; ?>_result');">
                            <span class="glyphicon glyphicon-edit"></span>
                            <?php echo Yii::t("app", "WYSIWYG"); ?>
                        </a>
                    </span>
                <?php endif; ?>

                <div class="table-result">
                    <?php
                        if ($check->table_result) {
                            $table = new ResultTable();
                            $table->parse($check->table_result);
                            echo $this->renderPartial("/project/target/check/tableresult", array("table" => $table, "check" => $check));
                        }
                    ?>
                </div>
            </td>
        </tr>
        <?php if ($this->_system->checklist_poc): ?>
            <tr>
                <th>
                    <?php echo Yii::t("app", "PoC"); ?>
                </th>
                <td>
                    <textarea name="TargetCheckEditForm_<?php echo $check->id; ?>[poc]" class="max-width wysiwyg" rows="10" id="TargetCheckEditForm_<?php echo $check->id; ?>_poc" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($check->poc); ?></textarea>
                </td>
            </tr>
        <?php endif; ?>
        <?php if ($this->_system->checklist_links): ?>
            <tr>
                <th>
                    <?php echo Yii::t("app", "Links"); ?>
                </th>
                <td>
                    <textarea name="TargetCheckEditForm_<?php echo $check->id; ?>[links]" class="max-width wysiwyg" rows="10" id="TargetCheckEditForm_<?php echo $check->id; ?>_links" <?php if (User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($check->links); ?></textarea>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (User::checkRole(User::ROLE_USER)): ?>
            <tr class="<?php echo $check->check->results ? '' : 'hide'; ?>" ">
                <th>
                    <?php echo Yii::t("app", "Insert Result"); ?>
                </th>
                <td class="text">
                    <ul class="results">
                        <?php foreach ($check->check->results as $result): ?>
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
                    <?php if (!$check->check->multiple_solutions): ?>
                        <li>
                            <div class="solution-header">
                                <label class="radio">
                                    <input name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="radio" value="0" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?> <?php if (!$check->solutions) echo "checked"; ?>>
                                    <?php echo Yii::t("app", "None"); ?>
                                </label>
                            </div>
                        </li>
                    <?php endif; ?>

                    <li>
                        <div class="solution-header">
                            <?php if ($check->check->multiple_solutions): ?>
                                <label class="checkbox">
                                    <input class="custom-solution" name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="checkbox" value="<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>" <?php if ($check->solution) echo "checked"; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
                            <?php else: ?>
                                <label class="radio">
                                    <input class="custom-solution" name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="radio" value="<?php echo TargetCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>" <?php if ($check->solution) echo "checked"; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
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
                            <input type="text" name="TargetCheckEditForm_<?php echo $check->id; ?>[solutionTitle]" class="max-width" id="TargetCheckEditForm_<?php echo $check->id; ?>_solutionTitle" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?> value="<?php echo CHtml::encode($check->solution_title); ?>">
                            <textarea name="TargetCheckEditForm_<?php echo $check->id; ?>[solution]" class="solution-edit wysiwyg max-width result" rows="10" id="TargetCheckEditForm_<?php echo $check->id; ?>_solution" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($check->solution); ?></textarea>

                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <label class="checkbox">
                                    <input name="TargetCheckEditForm_<?php echo $check->id; ?>[saveSolution]" type="checkbox" value="1" <?php if ($check->isRunning) echo "disabled"; ?>>
                                    <?php echo Yii::t("app", "Save As Generic"); ?>
                                </label>
                            <?php endif; ?>
                        </div>
                    </li>

                    <?php foreach ($check->check->solutions as $solution): ?>
                        <li>
                            <div class="solution-header">
                                <?php
                                    $checked = false;

                                    if ($check->solutions) {
                                        foreach ($check->solutions as $solutionValue) {
                                            if ($solutionValue->check_solution_id == $solution->id) {
                                                $checked = true;
                                                break;
                                            }
                                        }
                                    }
                                ?>
                                <?php if ($check->check->multiple_solutions): ?>
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
        <?php if (User::checkRole(User::ROLE_USER) || $check->attachments): ?>
            <tr>
                <th>
                    <?php echo Yii::t("app", "Attachments"); ?>
                </th>
                <td class="text">
                    <div class="file-input" id="upload-link-<?php echo $check->id; ?>">
                        <a href="#attachment"><?php echo Yii::t("app", "New Attachment"); ?></a>
                        <input type="file" name="TargetCheckAttachmentUploadForm[attachment]" data-id="<?php echo $check->id; ?>" data-upload-url="<?php echo $this->createUrl("project/uploadattachment", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $check->id)); ?>">
                    </div>

                    <div class="upload-message hide" id="upload-message-<?php echo $check->id; ?>"><?php echo Yii::t("app", "Uploading..."); ?></div>

                    <table class="table attachment-list<?php if (!$check->attachments) echo " hide"; ?>">
                        <tbody>
                            <?php if ($check->attachments): ?>
                                <?php foreach ($check->attachments as $attachment): ?>
                                    <tr data-path="<?php echo $attachment->path; ?>" data-control-url="<?php echo $this->createUrl("project/controlattachment"); ?>">
                                        <td class="info">
                                            <span contenteditable="true" class="single-line title" onblur="$(this).siblings('input').val($(this).text());">
                                                <?php echo CHtml::encode($attachment->title); ?>
                                            </span>
                                            <input type="hidden" name="TargetCheckEditForm_<?php echo $check->id; ?>[attachmentTitles][]" data-path="<?php echo $attachment->path; ?>" value="<?php echo CHtml::encode($attachment->title); ?>">
                                        </td>
                                        <td class="actions">
                                            <a href="<?php echo $this->createUrl("project/attachment", array("path" => $attachment->path)); ?>" title="<?php echo Yii::t("app", "Download"); ?>"><i class="icon icon-download"></i></a>
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
                                <input type="radio" name="TargetCheckEditForm_<?php echo $check->id; ?>[rating]" value="<?php echo $rating; ?>" <?php if (($check->rating == $rating) || ($rating == TargetCheck::RATING_NONE && !$check->rating)) echo "checked"; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "disabled"; ?>>
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
                    <button class="btn" onclick="user.check.save(<?php echo $check->id; ?>, true);" <?php if ($check->isRunning) echo "disabled"; ?>><?php echo Yii::t("app", "Save & Next"); ?></button>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<script>
    $.each($('.html_content'), function () {
        user.check.enableEditor($(this).attr('id'));
    });

    $('#TargetCheckEditForm_' + <?php echo $check->id; ?> + '_result').unbind('change input propertychange');
    $('#TargetCheckEditForm_' + <?php echo $check->id; ?> + '_result').bind('change input propertychange', function () {
        var val = $(this).val();
        var id = $(this).attr('id');

        if ($(this).val().isHTML()) {
            user.check.enableEditor(id);
        }
    });
</script>