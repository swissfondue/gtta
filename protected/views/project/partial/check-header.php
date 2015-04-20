<div id="check-<?php echo $tc->id; ?>" class="check-header <?php if ($tc->isRunning) echo "in-progress"; ?>" data-type="check" data-id="<?php echo $tc->id; ?>" data-control-url="<?php echo $this->createUrl("project/controlcheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $tc->id)); ?>" data-automated="<?php echo $check->automated ? 1 : 0; ?>" data-script-count="<?php echo count($check->scripts); ?>" data-check-url="<?php echo $this->createUrl("project/check", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $tc->id)); ?>">
    <table class="check-header">
        <tbody>
            <tr>
                <td class="name">
                    <?php if (User::checkRole(User::ROLE_USER)): ?>
                        <a href="#check" data-type="check-link" data-id="<?php echo $tc->id; ?>" onclick="user.check.toggle(<?php echo $tc->id; ?>);"><?php echo CHtml::encode($check->localizedName); ?><?php if ($number > 0) echo " " . ($number + 1); ?></a>
                    <?php else: ?>
                        <a href="#check" data-type="check-link" data-id="<?php echo $tc->id; ?>" onclick="client.check.toggle(<?php echo $tc->id; ?>);"><?php echo CHtml::encode($check->localizedName); ?><?php if ($number > 0) echo " " . ($number + 1); ?></a>
                    <?php endif; ?>

                    <?php if ($check->automated && User::checkRole(User::ROLE_USER)): ?>
                        <i class="icon-cog" title="<?php echo Yii::t("app", "Automated"); ?>"></i>
                    <?php endif; ?>

                    <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                        <a href="<?php echo $this->createUrl("check/editcheck", array("id" => $category->check_category_id, "control" => $check->check_control_id, "check" => $check->id)); ?>"><i class="icon-edit" title="<?php echo Yii::t("app", "Edit"); ?>"></i></a>
                    <?php endif; ?>

                    <?php if (User::checkRole(User::ROLE_USER) && $number == 0): ?>
                        <a href="#check-<?php echo $tc->id; ?>" onclick="user.check.copy(<?php echo $tc->id; ?>);"><i class="icon-plus" title="<?php echo Yii::t("app", "Copy"); ?>"></i></a>
                    <?php endif; ?>

                    <?php if (User::checkRole(User::ROLE_USER) && count($check->targetChecks) > 1): ?>
                        <a href="#check-<?php echo $tc->id; ?>" onclick="user.check.del(<?php echo $tc->id; ?>);"><i class="icon-remove" title="<?php echo Yii::t("app", "Delete"); ?>"></i></a>
                    <?php endif; ?>
                </td>
                <td class="status">
                    <?php if ($tc->status == TargetCheck::STATUS_FINISHED): ?>
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
                            $seconds = TargetCheckManager::getStartTime($tc->id);

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
                        <?php if ($check->automated): ?>
                            <?php if (!$tc->isRunning): ?>
                                <a href="#start" title="<?php echo Yii::t("app", "Start"); ?>" onclick="user.check.start(<?php echo $tc->id; ?>);"><i class="icon icon-play"></i></a>
                            <?php else: ?>
                                <a href="#stop" title="<?php echo Yii::t("app", "Stop"); ?>" onclick="user.check.stop(<?php echo $tc->id; ?>);"><i class="icon icon-stop"></i></a>
                            <?php endif; ?>
                            &nbsp;
                        <?php endif; ?>

                        <?php if (in_array($tc->status, array(TargetCheck::STATUS_OPEN, TargetCheck::STATUS_FINISHED))): ?>
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

<div class="check-form hide" data-type="check" data-id="<?php echo $tc->id; ?>" data-save-url="<?php echo $this->createUrl("project/savecheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $tc->id)); ?>" data-autosave-url="<?php echo $this->createUrl("project/autosavecheck", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id, "check" => $tc->id)); ?>"></div>