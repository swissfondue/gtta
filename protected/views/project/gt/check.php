<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.ui.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.iframe-transport.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.fileupload.js"></script>
<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<?php
    $limited = false;

    if ($this->_system->demo && !$check->check->demo) {
        $limited = true;
    }

    $started = ProjectGtCheckManager::getStartTime($check->projectChecks[0]->project_id, $check->projectChecks[0]->gt_check_id);
?>

<div class="container">
    <div class="row">
        <div class="span8">
            <div class="gt-check">
                <div class="module-header">
                    <div class="pull-right">
                        <?php if ($step == 1): ?>
                            <span class="disabled"><i class="icon icon-chevron-left"></i></span>
                        <?php else: ?>
                            <a href="#prev" onclick="user.gtCheck.prev();" title="<?php echo Yii::t('app', 'Previous'); ?>"><i class="icon icon-chevron-left"></i></a>&nbsp;
                        <?php endif; ?>

                        <span><?php echo $step; ?> / <?php echo $checkCount; ?></span>&nbsp;

                        <?php if ($step == $checkCount): ?>
                            <span class="disabled"><i class="icon icon-chevron-right"></i></span>
                        <?php else: ?>
                            <a href="#next" onclick="user.gtCheck.next();" title="<?php echo Yii::t('app', 'Next'); ?>"><i class="icon icon-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                    <?php echo CHtml::encode($module->module->localizedName); ?>
                </div>

                <?php if ($check->localizedDescription): ?>
                    <div class="check-description">
                        <?php echo CHtml::encode($check->localizedDescription); ?>
                    </div>
                <?php endif; ?>

                <div class="check-header <?php if ($check->isRunning) echo 'in-progress'; ?> <?php if ($limited) echo "limited"; ?>">
                    <table class="check-header">
                        <tbody>
                            <tr>
                                <td class="name">
                                    <?php echo CHtml::encode($check->check->localizedName); ?>

                                    <?php if ($check->check->automated && User::checkRole(User::ROLE_USER)): ?>
                                        <i class="icon-cog" title="<?php echo Yii::t('app', 'Automated'); ?>"></i>
                                    <?php endif; ?>

                                    <?php if (User::checkRole(User::ROLE_ADMIN) && !$limited): ?>
                                        <a href="<?php echo $this->createUrl('check/editcheck', array('id' => $check->check->control->check_category_id, 'control' => $check->check->check_control_id, 'check' => $check->check_id)); ?>"><i class="icon-edit" title="<?php echo Yii::t('app', 'Edit'); ?>"></i></a>
                                    <?php endif; ?>
                                </td>
                                <td class="status">
                                    <?php if (!$limited && $check->projectChecks && $check->projectChecks[0]->status == ProjectGtCheck::STATUS_FINISHED): ?>
                                        <?php
                                            switch ($check->projectChecks[0]->rating) {
                                                case ProjectGtCheck::RATING_INFO:
                                                    echo '<span class="label label-info">' . $ratings[ProjectGtCheck::RATING_INFO] . '</span>';
                                                    break;

                                                case ProjectGtCheck::RATING_LOW_RISK:
                                                    echo '<span class="label label-low-risk">' . $ratings[ProjectGtCheck::RATING_LOW_RISK] . '</span>';
                                                    break;

                                                case ProjectGtCheck::RATING_MED_RISK:
                                                    echo '<span class="label label-med-risk">' . $ratings[ProjectGtCheck::RATING_MED_RISK] . '</span>';
                                                    break;

                                                case ProjectGtCheck::RATING_HIGH_RISK:
                                                    echo '<span class="label label-high-risk">' . $ratings[ProjectGtCheck::RATING_HIGH_RISK] . '</span>';
                                                    break;

                                                default:
                                                    echo '<span class="label">' . $ratings[$check->projectChecks[0]->rating] . '</span>';
                                                    break;
                                            }
                                        ?>
                                    <?php elseif ($check->isRunning): ?>
                                        <?php
                                            if ($started) {
                                                $seconds = time() - strtotime($started);
                                                $minutes = 0;

                                                if ($seconds > 59) {
                                                    $minutes = floor($seconds / 60);
                                                    $seconds = $seconds - ($minutes * 60);
                                                }

                                                printf('%02d:%02d', $minutes, $seconds);
                                            }
                                            else
                                                echo '00:00';
                                        ?>
                                    <?php else: ?>
                                        &nbsp;
                                    <?php endif; ?>
                                </td>
                                <?php if (User::checkRole(User::ROLE_USER)): ?>
                                    <td class="actions">
                                        <?php if (!$limited && $check->check->automated): ?>
                                            <?php if (!$check->projectChecks || $check->projectChecks && in_array($check->projectChecks[0]->status, array(ProjectGtCheck::STATUS_OPEN, ProjectGtCheck::STATUS_FINISHED))): ?>
                                                <a href="#start" title="<?php echo Yii::t('app', 'Start'); ?>" onclick="user.gtCheck.start();"><i class="icon icon-play"></i></a>
                                            <?php elseif ($check->projectChecks && $check->projectChecks[0]->isRunning): ?>
                                                <a href="#stop" title="<?php echo Yii::t('app', 'Stop'); ?>" onclick="user.gtCheck.stop();"><i class="icon icon-stop"></i></a>
                                            <?php else: ?>
                                                <span class="disabled"><i class="icon icon-stop" title="<?php echo Yii::t('app', 'Stop'); ?>"></i></span>
                                            <?php endif; ?>
                                            &nbsp;
                                        <?php endif; ?>

                                        <?php if (!$limited && $check->projectChecks && in_array($check->projectChecks[0]->status, array(ProjectGtCheck::STATUS_OPEN, ProjectGtCheck::STATUS_FINISHED))): ?>
                                            <a href="#reset" title="<?php echo Yii::t('app', 'Reset'); ?>" onclick="user.gtCheck.reset();"><i class="icon icon-refresh"></i></a>
                                        <?php else: ?>
                                            <span class="disabled"><i class="icon icon-refresh" title="<?php echo Yii::t('app', 'Reset'); ?>"></i></span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="check-form" data-control-url="<?php echo $this->createUrl('project/gtcontrolcheck', array('id' => $project->id, 'module' => $module->gt_module_id, 'check' => $check->id)); ?>" data-type="<?php echo $check->check->automated ? 'automated' : 'manual'; ?>" data-save-url="<?php echo $this->createUrl('project/gtsavecheck', array('id' => $project->id, 'module' => $module->gt_module_id, 'check' => $check->id)); ?>" data-autosave-url="<?php echo $this->createUrl('project/gtautosavecheck', array('id' => $project->id, 'module' => $module->gt_module_id, 'check' => $check->id)); ?>">
                <?php if ($limited): ?>
                    <?php echo Yii::t("app", "This check is not available in the demo version."); ?>
                <?php else: ?>
                    <table class="table check-form">
                        <tbody>
                            <tr>
                                <th>
                                    <?php echo Yii::t('app', 'Reference'); ?>
                                </th>
                                <td class="text">
                                    <?php
                                        $reference = $check->check->_reference->name . ( $check->check->reference_code ? '-' . $check->check->reference_code : '' );
                                        $referenceUrl = '';

                                        if ($check->check->reference_code && $check->check->reference_url)
                                            $referenceUrl = $check->check->reference_url;
                                        else if ($check->check->_reference->url)
                                            $referenceUrl = $check->check->_reference->url;

                                        if ($referenceUrl)
                                            $reference = '<a href="' . $referenceUrl . '" target="_blank">' . CHtml::encode($reference) . '</a>';
                                        else
                                            $reference = CHtml::encode($reference);

                                        echo $reference;
                                    ?>
                                </td>
                            </tr>
                            <?php if ($check->check->localizedBackgroundInfo): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Background Info'); ?>
                                    </th>
                                    <td class="text">
                                        <div class="limiter"><?php echo $check->check->localizedBackgroundInfo; ?></div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($check->check->localizedHints): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Hints'); ?>
                                    </th>
                                    <td class="text">
                                        <div class="limiter"><?php echo $check->check->localizedHints; ?></div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($check->check->localizedQuestion): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Question'); ?>
                                    </th>
                                    <td class="text">
                                        <div class="limiter"><?php echo $check->check->localizedQuestion; ?></div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th>
                                    <?php echo Yii::t('app', 'Target'); ?>
                                </th>
                                <td>
                                    <input type="text" class="max-width" name="ProjectGtCheckEditForm[target]" id="ProjectGtCheckEditForm_target" value="<?php if ($check->projectChecks) echo CHtml::encode($check->projectChecks[0]->target); ?>" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo 'readonly'; ?>>

                                    <?php if ($check->localizedTargetDescription): ?>
                                        <p class="help-block">
                                            <?php echo CHtml::encode($check->localizedTargetDescription); ?>
                                        </p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if ($check->check->automated): ?>
                                <?php if ($check->check->protocol): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Protocol'); ?>
                                        </th>
                                        <td>
                                            <input type="text" class="input-xlarge" name="ProjectGtCheckEditForm[protocol]" id="ProjectGtCheckEditForm_protocol" value="<?php echo CHtml::encode($check->projectChecks ? $check->projectChecks[0]->protocol : $check->protocol); ?>" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo 'readonly'; ?>>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($check->check->port): ?>
                                    <tr>
                                        <th>
                                            <?php echo Yii::t('app', 'Port'); ?>
                                        </th>
                                        <td>
                                            <input type="text" class="input-xlarge" name="ProjectGtCheckEditForm[port]" id="ProjectGtCheckEditForm_port" value="<?php echo $check->projectChecks ? $check->projectChecks[0]->port : $check->port; ?>" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo 'readonly'; ?>>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($check->check->scripts && $check->check->automated && User::checkRole(User::ROLE_USER)): ?>
                                <?php foreach ($check->check->scripts as $script): ?>
                                    <?php
                                        if (!$script->inputs) {
                                            continue;
                                        }
                                    ?>
                                    <?php if (count($check->check->scripts) > 1): ?>
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
                                                $value = '';

                                                if ($input->projectInputs) {
                                                    foreach ($input->projectInputs as $inputValue) {
                                                        if ($inputValue->check_input_id == $input->id) {
                                                            $value = $inputValue->value;
                                                            break;
                                                        }
                                                    }
                                                }

                                                if ($value == NULL && $input->value != NULL)
                                                    $value = $input->value;

                                                if ($value != NULL)
                                                    $value = CHtml::encode($value);
                                            ?>
                                            <input type="text" name="ProjectGtCheckEditForm[inputs][<?php echo $input->id; ?>]" class="max-width" id="ProjectGtCheckEditForm_inputs_<?php echo $input->id; ?>" <?php if ($check->isRunning) echo 'readonly'; ?> value="<?php echo $value; ?>">
                                        <?php elseif ($input->type == CheckInput::TYPE_TEXTAREA): ?>
                                            <?php
                                                $value = '';

                                                if ($input->projectInputs) {
                                                    foreach ($input->projectInputs as $inputValue) {
                                                        if ($inputValue->check_input_id == $input->id) {
                                                            $value = $inputValue->value;
                                                            break;
                                                        }
                                                    }
                                                }

                                                if ($value == NULL && $input->value != NULL)
                                                    $value = $input->value;

                                                if ($value != NULL)
                                                    $value = CHtml::encode($value);
                                            ?>
                                            <textarea wrap="off" name="ProjectGtCheckEditForm[inputs][<?php echo $input->id; ?>]" class="max-width" rows="2" id="ProjectGtCheckEditForm_inputs_<?php echo $input->id; ?>" <?php if ($check->isRunning) echo 'readonly'; ?>><?php echo $value; ?></textarea>
                                        <?php elseif (in_array($input->type, array(CheckInput::TYPE_CHECKBOX, CheckInput::TYPE_FILE))): ?>
                                            <?php
                                                $value = '';

                                                if ($input->projectInputs) {
                                                    foreach ($input->projectInputs as $inputValue) {
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

                                            <input type="checkbox" name="ProjectGtCheckEditForm[inputs][<?php echo $input->id; ?>]" id="ProjectGtCheckEditForm_inputs_<?php echo $input->id; ?>" <?php if ($check->isRunning) echo 'readonly'; ?> value="1"<?php if ($value) echo ' checked'; ?>>

                                            <?php if ($currentGroup !== false): ?>
                                                        <?php echo CHtml::encode($input->localizedName); ?>
                                                    </label>
                                                </div>
                                            <?php endif; ?>
                                        <?php elseif ($input->type == CheckInput::TYPE_RADIO): ?>
                                            <?php
                                                $value = '';

                                                if ($input->projectInputs) {
                                                    foreach ($input->projectInputs as $inputValue) {
                                                        if ($inputValue->check_input_id == $input->id) {
                                                            $value = $inputValue->value;
                                                            break;
                                                        }
                                                    }
                                                }

                                                $radioBoxes = explode("\n", str_replace("\r", '', $input->value));
                                            ?>

                                            <ul class="radio-input">
                                                <?php foreach ($radioBoxes as $radio): ?>
                                                    <li>
                                                        <label class="radio">
                                                            <input name="ProjectGtCheckEditForm[inputs][<?php echo $input->id; ?>]" type="radio" value="<?php echo CHtml::encode($radio); ?>" <?php if ($check->isRunning) echo 'disabled'; ?> <?php if ($value == $radio) echo ' checked'; ?>>
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
                                    <?php echo Yii::t('app', 'Result'); ?>
                                </th>
                                <td>
                                    <textarea name="ProjectGtCheckEditForm[result]" class="max-width result" rows="10" id="ProjectGtCheckEditForm_result" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo 'readonly'; ?>><?php if ($check->projectChecks) echo $check->projectChecks[0]->result; ?></textarea>

                                    <?php
                                        $showAuto = false;

                                        if ($check->check->automated && $check->projectChecks && $started && $check->projectChecks[0]->isRunning) {
                                            $showAuto = true;
                                        }
                                    ?>

                                    <div class="automated-info-block <?php if (!$showAuto) echo "hide"; ?>">
                                        <?php
                                            if ($showAuto) {
                                                $dt = new DateTime($started);
                                                $user = $check->projectChecks[0]->user;

                                                echo Yii::t("app", "Started by {user} on {date} at {time}", array(
                                                    "{user}" => $user->name ? $user->name : $user->email,
                                                    "{date}" => $dt->format("d.m.Y"),
                                                    "{time}" => $dt->format("H:i:s"),
                                                ));
                                            }
                                        ?>
                                    </div>

                                    <div class="table-result">
                                        <?php
                                            if ($check->projectChecks && $check->projectChecks[0]->table_result) {
                                                $table = new ResultTable();
                                                $table->parse($check->projectChecks[0]->table_result);
                                                echo $this->renderPartial('/project/gt/tableresult', array('table' => $table));
                                            }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <?php if ($check->check->results && User::checkRole(User::ROLE_USER)): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Insert Result'); ?>
                                    </th>
                                    <td class="text">
                                        <ul class="results">
                                            <?php foreach ($check->check->results as $result): ?>
                                                <li>
                                                    <div class="result-header">
                                                        <a href="#insert" onclick="user.gtCheck.insertResult($('.result-content[data-id=<?php echo $result->id; ?>]').html());"><?php echo CHtml::encode($result->localizedTitle); ?></a>

                                                        <span class="result-control" data-id="<?php echo $result->id; ?>">
                                                            <a href="#result" onclick="user.gtCheck.expandResult(<?php echo $result->id; ?>);"><i class="icon-chevron-down"></i></a>
                                                        </span>
                                                    </div>

                                                    <div class="result-content hide" data-id="<?php echo $result->id; ?>"><?php echo str_replace("\n", '<br>', CHtml::encode($result->localizedResult)); ?></div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th>
                                    <?php echo Yii::t('app', 'Solution'); ?>
                                </th>
                                <td class="text">
                                    <ul class="solutions">
                                        <?php if (!$check->check->multiple_solutions): ?>
                                            <li>
                                                <div class="solution-header">
                                                    <label class="radio">
                                                        <input name="ProjectGtCheckEditForm[solutions][]" type="radio" value="0" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo 'disabled'; ?> <?php if (!$check->projectCheckSolutions) echo 'checked'; ?>>
                                                        <?php echo Yii::t('app', 'None'); ?>
                                                    </label>
                                                </div>
                                            </li>
                                        <?php endif; ?>

                                        <li>
                                            <div class="solution-header">
                                                <?php if ($check->check->multiple_solutions): ?>
                                                    <label class="checkbox">
                                                        <input class="custom-solution" name="ProjectGtCheckEditForm[solutions][]" type="checkbox" value="<?php echo ProjectGtCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>" <?php if ($check->projectChecks && $check->projectChecks[0]->solution) echo 'checked'; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo 'disabled'; ?>>
                                                <?php else: ?>
                                                    <label class="radio">
                                                        <input class="custom-solution" name="ProjectGtCheckEditForm[solutions][]" type="radio" value="<?php echo ProjectGtCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>" <?php if ($check->projectChecks && $check->projectChecks[0]->solution) echo 'checked'; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo 'disabled'; ?>>
                                                <?php endif; ?>
                                                    <?php echo Yii::t("app", "Custom Solution"); ?>

                                                    <span class="solution-control" data-id="<?php echo ProjectGtCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>">
                                                        <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                            <a href="#solution" onclick="user.gtCheck.expandSolution('<?php echo ProjectGtCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>');"><i class="icon-chevron-down"></i></a>
                                                        <?php else: ?>
                                                            <a href="#solution" onclick="client.gtCheck.expandSolution('<?php echo ProjectGtCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>');"><i class="icon-chevron-down"></i></a>
                                                        <?php endif; ?>
                                                    </span>
                                                </label>
                                            </div>

                                            <div class="solution-content hide" data-id="<?php echo ProjectGtCheckEditForm::CUSTOM_SOLUTION_IDENTIFIER; ?>">
                                                <input type="text" name="ProjectGtCheckEditForm[solutionTitle]" class="max-width" id="ProjectGtCheckEditForm_solutionTitle" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo 'readonly'; ?> value="<?php echo $check->projectChecks ? CHtml::encode($check->projectChecks[0]->solution_title) : ""; ?>">
                                                <textarea name="ProjectGtCheckEditForm[solution]" class="solution-edit max-width result" rows="10" id="ProjectGtCheckEditForm_solution" <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo 'readonly'; ?>><?php echo $check->projectChecks ? CHtml::encode($check->projectChecks[0]->solution) : ""; ?></textarea>

                                                <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                                    <label class="checkbox">
                                                        <input name="ProjectGtCheckEditForm[saveSolution]" type="checkbox" value="1" <?php if ($check->isRunning) echo 'disabled'; ?>>
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

                                                        if ($check->projectCheckSolutions) {
                                                            foreach ($check->projectCheckSolutions as $solutionValue) {
                                                                if ($solutionValue->check_solution_id == $solution->id) {
                                                                    $checked = true;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                    <?php if ($check->check->multiple_solutions): ?>
                                                        <label class="checkbox">
                                                            <input name="ProjectGtCheckEditForm[solutions][]" type="checkbox" value="<?php echo $solution->id; ?>" <?php if ($checked) echo 'checked'; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo 'disabled'; ?>>
                                                    <?php else: ?>
                                                        <label class="radio">
                                                            <input name="ProjectGtCheckEditForm[solutions][]" type="radio" value="<?php echo $solution->id; ?>" <?php if ($checked) echo 'checked'; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo 'disabled'; ?>>
                                                    <?php endif; ?>
                                                        <?php echo CHtml::encode($solution->localizedTitle); ?>

                                                        <span class="solution-control" data-id="<?php echo $solution->id; ?>">
                                                            <?php if (User::checkRole(User::ROLE_USER)): ?>
                                                                <a href="#solution" onclick="user.gtCheck.expandSolution(<?php echo $solution->id; ?>);"><i class="icon-chevron-down"></i></a>
                                                            <?php else: ?>
                                                                <a href="#solution" onclick="client.gtCheck.expandSolution(<?php echo $solution->id; ?>);"><i class="icon-chevron-down"></i></a>
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
                            <?php if (User::checkRole(User::ROLE_USER) || $check->projectCheckAttachments): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Attachments'); ?>
                                    </th>
                                    <td class="text">
                                        <div class="file-input" id="upload-link">
                                            <a href="#attachment"><?php echo Yii::t('app', 'New Attachment'); ?></a>
                                            <input type="file" name="ProjectGtCheckAttachmentUploadForm[attachment]" data-upload-url="<?php echo $this->createUrl('project/gtuploadattachment', array('id' => $project->id, 'module' => $module->gt_module_id, 'check' => $check->id)); ?>">
                                        </div>

                                        <div class="upload-message hide" id="upload-message"><?php echo Yii::t('app', 'Uploading...'); ?></div>

                                        <table class="table attachment-list<?php if (!$check->projectCheckAttachments) echo ' hide'; ?>">
                                            <tbody>
                                                <?php if ($check->projectCheckAttachments): ?>
                                                    <?php foreach ($check->projectCheckAttachments as $attachment): ?>
                                                        <tr data-path="<?php echo $attachment->path; ?>" data-control-url="<?php echo $this->createUrl('project/gtcontrolattachment'); ?>">
                                                            <td class="info">
                                                                <span contenteditable="true" class="single-line title" onblur="$(this).siblings('input').val($(this).text());">
                                                                    <?php echo CHtml::encode($attachment->title); ?>
                                                                </span>
                                                                <input type="hidden" name="ProjectGtCheckEditForm[attachmentTitles][]" data-path="<?php echo $attachment->path; ?>" value="<?php echo CHtml::encode($attachment->title); ?>">
                                                                <div class="name content">
                                                                    <a href="<?php echo $this->createUrl('project/gtattachment', array('path' => $attachment->path)); ?>"><?php echo CHtml::encode($attachment->name); ?></a>
                                                                </div>
                                                            </td>
                                                            <td class="actions">
                                                                <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="user.gtCheck.delAttachment('<?php echo $attachment->path; ?>');"><i class="icon icon-remove"></i></a>
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
                                    <?php echo Yii::t('app', 'Result Rating'); ?>
                                </th>
                                <td class="text">
                                    <ul class="rating">
                                        <?php foreach (ProjectGtCheck::getValidRatings() as $rating): ?>
                                            <li>
                                                <label class="radio">
                                                    <input type="radio" name="ProjectGtCheckEditForm[rating]" value="<?php echo $rating; ?>" <?php if (($check->projectChecks && $check->projectChecks[0]->rating == $rating) || ($rating == ProjectGtCheck::RATING_NONE && (!$check->projectChecks || !$check->projectChecks[0]->rating))) echo 'checked'; ?> <?php if ($check->isRunning || User::checkRole(User::ROLE_CLIENT)) echo 'disabled'; ?>>
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
                                        <button class="btn" onclick="user.gtCheck.save();" <?php if ($check->isRunning) echo 'disabled'; ?>><?php echo Yii::t('app', 'Save'); ?></button>&nbsp;
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <?php if (User::checkRole(User::ROLE_USER)): ?>
                <div class="suggested-targets <?php if (!$check->suggestedTargets) echo 'hide'; ?>">
                    <div class="suggested-targets-header">
                        <?php echo Yii::t('app', 'Suggested Targets'); ?>
                    </div>

                    <div class="suggested-targets-body">
                        <table class="table suggested-target-list">
                            <tbody>
                                <?php if ($check->suggestedTargets): ?>
                                    <?php foreach ($check->suggestedTargets as $target): ?>
                                        <tr data-id="<?php echo $target->id; ?>" data-control-url="<?php echo $this->createUrl('project/gtcontroltarget'); ?>">
                                            <td class="target">
                                                <?php echo CHtml::encode($target->target); ?> /
                                                <a href="#"><?php echo CHtml::encode($target->module->localizedName); ?></a>
                                            </td>
                                            <td class="actions">
                                                <?php if (!$target->approved): ?>
                                                    <a href="#approve" id="approve-link" title="<?php echo Yii::t('app', 'Approve'); ?>" onclick="user.gtCheck.approveTarget('<?php echo $target->id; ?>');"><i class="icon icon-ok"></i></a>&nbsp;
                                                <?php endif; ?>

                                                <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="user.gtCheck.delTarget('<?php echo $target->id; ?>');"><i class="icon icon-remove"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="span4">
            <?php if ($module->module->suggestedTargets): ?>
                <div id="suggested-targets-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#suggested-targets');"><i class="icon-chevron-up"></i></div>
                <h3><a href="#toggle" onclick="system.toggleBlock('#suggested-targets');"><?php echo Yii::t('app', 'Suggested Targets'); ?></a></h3>

                <div class="info-block" id="suggested-targets">
                    <ul class="suggested-targets">
                        <?php foreach ($module->module->suggestedTargets as $target): ?>
                            <li>
                                <?php echo CHtml::encode($target->target); ?> /
                                <a href="#"><?php echo CHtml::encode($target->check->check->localizedName); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php
                echo $this->renderPartial("partial/right-block", array(
                    "quickTargets" => null,
                    "project" => $project,
                    "client" => $client,
                    "statuses" => $statuses,
                    "category" => null,
                    "target" => null
                ));
            ?>
        </div>
    </div>
</div>

<?php if (User::checkRole(User::ROLE_USER)): ?>
    <script>
        var ratings = {
            <?php
                $ratingNames = array();

                foreach ($ratings as $k => $v) {
                    $class = null;

                    switch ($k) {
                        case ProjectGtCheck::RATING_INFO:
                            $class = 'label-info';
                            break;

                        case ProjectGtCheck::RATING_LOW_RISK:
                            $class = 'label-low-risk';
                            break;

                        case ProjectGtCheck::RATING_MED_RISK:
                            $class = 'label-med-risk';
                            break;

                        case ProjectGtCheck::RATING_HIGH_RISK:
                            $class = 'label-high-risk';
                            break;
                    }

                    $ratingNames[] = $k . ':' . json_encode(array(
                        'text' => CHtml::encode($v),
                        'classN' => $class
                    ));
                }

                echo implode(',', $ratingNames);
            ?>
        };

        $(function () {
            user.gtCheck.initProjectGtCheckAttachmentUploadForms();
            user.gtCheck.initAutosave();

            <?php
                if ($check->isRunning):
                    $time = -1;

                    if ($started) {
                        $time = new DateTime($started);
                        $now = new DateTime();

                        $time = $now->format("U") - $time->format("U");
                    }
            ?>
                user.gtCheck.runningCheck = {
                    'time': <?php echo $time; ?>
                };
            <?php endif; ?>

            setTimeout(function () {
                user.gtCheck.update('<?php echo $this->createUrl('project/gtupdatechecks', array('id' => $project->id, 'module' => $module->gt_module_id, 'check' => $check->id)); ?>');
            }, 1000);

            $(".solution-edit").ckeditor();
        });
    </script>
<?php endif; ?>