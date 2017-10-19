<?php $goToNext = isset($goToNext) ? $goToNext : false ?>

<table class="table check-form">
    <tbody>
        <tr>
            <th>
                <?php echo Yii::t("app", "Id"); ?>
            </th>
            <td class="text">
                <?php echo CHtml::encode($check->id); ?>
                <input type="hidden" name="TargetCheckEditForm_<?php echo $check->id ?>[lastModified]" id="TargetCheckEditForm_<?php echo $check->id ?>[lastModified]" value="<?php echo $check->last_modified; ?>">
            </td>
            </tr>
            <tr>
            <th>
                <?php echo Yii::t("app", "Reference"); ?>
            </th>
            <td class="text">
                <?php
                    $reference = $checkData->_reference->name . ($checkData->reference_code ? "-" . $checkData->reference_code : "");
                    $referenceUrl = "";

                    if ($checkData->reference_code && $checkData->reference_url) {
                        $referenceUrl = $checkData->reference_url;
                    } else if ($checkData->_reference->url) {
                        $referenceUrl = $checkData->_reference->url;
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

        <?php foreach ($fields as $field): ?>
            <?=
                $this->renderPartial("partial/check-field", [
                    "field" => $field,
                    "targetCheck" => $check,
                    "hidden" => true
                ]);
            ?>
        <?php endforeach; ?>

        <tr>
            <td>&nbsp;</td>
            <td>
                <div class="table-result">
                    <?php
                        if ($check->table_result) {
                            $table = new ResultTable();
                            $table->parse($check->table_result);
                            echo $this->renderPartial("/project/target/check/tableresult", ["table" => $table, "check" => $check]);
                        }
                    ?>
                </div>
            </td>
        </tr>

        <?php if ($checkData->scripts && $checkData->automated && User::checkRole(User::ROLE_USER)): ?>
            <?php foreach ($check->scripts as $script): ?>
                <tr class="script-inputs">
                    <th>
                        <label class="checkbox">
                            <input name="TargetCheckEditForm_<?php echo $check->id ?>[scripts][]" id="TargetCheckEditForm_<?php print $check->id; ?>_scripts_<?php print $script->script->id; ?>" type="checkbox" data-id="<?php print $script->script->id; ?>" value="<?php echo $script->script->id; ?>" <?php if ($script->start) echo 'checked="checked"'; ?> <?php if ($check->isRunning) echo "disabled"; ?> />
                            <?php echo CHtml::encode($script->script->package->name); ?>
                        </label>
                    </th>
                    <td>
                        <div class="pull-left">
                            <input style="width:70px;" type="text" name="TargetCheckEditForm_<?php echo $check->id; ?>[timeouts][]" id="TargetCheckEditForm_<?php echo $check->id; ?>_timeouts_<?php echo $script->script->id; ?>" data-script-id="<?php echo $script->script->id; ?>" <?php if ($check->isRunning) echo "readonly"; ?> value="<?php echo $script->timeout ? $script->timeout : $script->script->package->timeout; ?>">
                        </div>
                        <div class="pull-left" style="padding-top:5px;padding-left:5px;">
                            <?= Yii::t("app", "seconds timeout"); ?>
                        </div>
                    </td>
                </tr>

                <?php
                    $groups = array();
                    $group = array();
                    $inputs = $script->script->inputs;

                    if (count($inputs) > Yii::app()->params["maxCheckboxes"]) {
                        foreach ($inputs as $input) {
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
                <?php foreach ($inputs as $input): ?>
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
                                    $value = $inputValue->value;
                                    break;
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
                                    $value = $inputValue->value;
                                    break;
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
                                    $value = $inputValue->value;
                                    break;
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
                                    $value = $inputValue->value;
                                    break;
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

        <?php if (User::checkRole(User::ROLE_USER)): ?>
            <tr class="<?php echo $results ? '' : 'hide'; ?>" ">
                <th>
                    <?php echo Yii::t("app", "Insert Result"); ?>
                </th>
                <td class="text">
                    <ul class="results">
                        <?php foreach ($results as $result): ?>
                            <li>
                                <div class="result-header">
                                    <a href="#insert" onclick="user.check.insertResult(<?php echo $check->id; ?>, $('.result-content[data-id=<?php echo $result->id; ?>]').html());"><?php echo CHtml::encode($result->localizedTitle); ?></a>

                                    <span class="result-control" data-id="<?php echo $result->id; ?>">
                                        <a href="#result" onclick="user.check.expandResult(<?php echo $result->id; ?>);"><i class="icon-chevron-down"></i></a>
                                    </span>
                                </div>

                                <div class="result-content hide" data-id="<?php echo $result->id; ?>"><?php echo (Utils::isHtml($result->localizedResult) ? $result->localizedResult : str_replace("\n", "<br>", $result->localizedResult)); ?></div>
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
                    <?php if (!$checkData->multiple_solutions): ?>
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
                            <?php if ($checkData->multiple_solutions): ?>
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
                            <input type="text" name="TargetCheckEditForm_<?php echo $check->id; ?>[solutionTitle]" class="max-width" id="TargetCheckEditForm_<?php echo $check->id; ?>_solutionTitle" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?> value="<?php echo CHtml::encode($check->solutionTitle); ?>">
                            <textarea name="TargetCheckEditForm_<?php echo $check->id; ?>[solution]" class="solution-edit wysiwyg max-width result" rows="10" id="TargetCheckEditForm_<?php echo $check->id; ?>_solution" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo "readonly"; ?>><?php echo CHtml::encode($check->solution); ?></textarea>

                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <label class="checkbox">
                                    <input name="TargetCheckEditForm_<?php echo $check->id; ?>[saveSolution]" type="checkbox" value="1" <?php if ($check->isRunning) echo "disabled"; ?>>
                                    <?php echo Yii::t("app", "Save As Generic"); ?>
                                </label>
                            <?php endif; ?>
                        </div>
                    </li>

                    <?php foreach ($solutions as $solution): ?>
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
                                <?php if ($checkData->multiple_solutions): ?>
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
                    <br>
                    <a id="additionalSolutions" href="#additionalSolutionFields"><?php echo Yii::t("app", "Additional solution fields"); ?><i class="icon-chevron-down pull-right"></i></a>
                </ul>
            </td>
        </tr>
        <div id="additionalSolutionFields">
        <?php $this->renderPartial(
            "partial/check-field", [
                "field" => $check->getField(GlobalCheckField::FIELD_TECHNICAL_SOLUTION),
                "targetCheck" => $check, "hidden" => false
            ]
        ); ?>
        <?php $this->renderPartial(
            "partial/check-field", [
                "field" => $check->getField(GlobalCheckField::FIELD_MANAGEMENT_SOLUTION),
                "targetCheck" => $check, "hidden" => false
            ]
        ); ?>
        </div>
        <?php if (User::checkRole(User::ROLE_USER) || $check->attachments): ?>
            <tr>
                <th>
                    <?php echo Yii::t("app", "Attachments"); ?>
                </th>
                <td class="text">
                    <div class="file-input" id="upload-link-<?php echo $check->id; ?>">
                        <a href="#attachment"><?php echo Yii::t("app", "New Attachment"); ?></a>
                        <input type="file" name="TargetCheckAttachmentUploadForm[attachment]" accept="image/*,.txt" data-id="<?php echo $check->id; ?>" data-upload-url="<?php echo $this->createUrl("project/uploadattachment", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $check->id)); ?>">
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
                <button class="btn" onclick="user.check.cvss(<?php echo $check->id; ?>);">Set CVSS 3.0 Vector</button>
                <br><br>
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
                    <?php if ($goToNext): ?>
                        <button class="btn" onclick="user.check.save(<?php echo $check->id; ?>, true);" <?php if ($check->isRunning) echo "disabled"; ?>><?php echo Yii::t("app", "Save & Next"); ?></button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<script>
    $(function () {
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
        var id = <?= $check->id ?>;
        var form = $("div.check-form[data-type=check][data-id=" + id + "]");

        $(".wysiwyg", form).ckeditor();
        user.check.initTargetCheckAttachmentUploadForms(id);
        user.check.initAutosave(id);

        $('#technical_solution').hide();
        $('#management_solution').hide();

        $('#additionalSolutions').on("click",function(event){
            event.preventDefault();
            console.log('some');
            $('#technical_solution').toggle();
            $('#management_solution').toggle();
            //post code
        })
    });


</script>
