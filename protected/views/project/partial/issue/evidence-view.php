<div id="<?= $host ?>" class="target-group" style="margin-bottom: 20px;">
    <h3><?= Yii::t("app", "Evidences for {ip}", ["{ip}" => $host]) ?></h3>

    <ul class="nav nav-tabs target-tabs">
        <?php $i = 0; ?>
        <?php foreach ($evidences as $evidence): ?>
            <li id="evidence_tab_<?= $evidence->id; ?>" class="<?= $i == 0 ? "active" : "" ?>">
                <a href="#evidence_<?= $evidence->id; ?>">#<?= $i; ?></a>
            </li>
            <?php $i++; ?>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content tab-content-evidence-view">
        <?php $i = 0; ?>

        <?php foreach ($evidences as $evidence): ?>
            <?php $tc = $evidence->targetCheck; ?>

            <div class="tab-pane <?= $i == 0 ? "active" : "" ?>" id="evidence_<?= $evidence->id; ?>"
                 data-target-check-id="<?= $tc->id ?>"
                 data-update-evidence-url-<?= $evidence->id ?>="<?php echo $this->createUrl("project/updateevidence", ["id" => $project->id, "issue" => $issue->id, "evidence" => $evidence->id]) ?> ">
                <div class="control-group">
                    <span style="font-size: 16px; margin-left: 5px;"><?= Yii::t("app", "Evidence for this instance") ?></span>&nbsp;—&nbsp;
                    <a href="<?= $this->createUrl("project/evidence", ["id" => $project->id, "issue" => $issue->id, "evidence" => $evidence->id]) ?>"><?= Yii::t("app", "Edit") ?></a>
                    <a class="red" href="#"
                       onclick="admin.issue.evidence.delete('<?= $this->createUrl("project/controlevidence") ?>', <?= $evidence->id ?>, function () { system.redirect('<?= $this->createUrl("project/issue", ["id" => $project->id, "issue" => $issue->id]) ?>') });"><?= Yii::t("app", "Delete") ?></a>

                    <?php if ($tc->check->automated): ?>
                        <div style="float:right">
                            <div class="run-info inline">
                                <div class="check-time"></div>
                            </div>
                            <div class="run-buttons inline">
                                <a href="#start" title="<?php echo Yii::t("app", "Start"); ?>"
                                   class="start-button <?= $tc->isRunning ? "hide" : "" ?>"
                                   onclick="admin.issue.evidence.start('<?= $this->createUrl("project/controlcheck", ["id" => $project->id, "target" => $tc->target_id, "category" => $tc->check->control->check_category_id, "check" => $tc->id]); ?>', <?= $tc->id; ?>);"><i
                                            class="icon icon-play"></i></a>
                                <a href="#stop" title="<?php echo Yii::t("app", "Stop"); ?>"
                                   class="stop-button <?= !$tc->isRunning ? "hide" : "" ?>"
                                   onclick="admin.issue.evidence.stop('<?= $this->createUrl("project/controlcheck", ["id" => $project->id, "target" => $tc->target_id, "category" => $tc->check->control->check_category_id, "check" => $tc->id]); ?>', <?php echo $tc->id; ?>);"><i
                                            class="icon icon-stop"></i></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="issue-details">
                        <?php if ($tc->transportProtocol || $tc->applicationProtocol): ?>
                            <div class="protocols">
                                <?= $tc->transportProtocol; ?>

                                <?php if ($tc->transportProtocol && $tc->applicationProtocol): ?>/<?php endif; ?>

                                <?= $tc->applicationProtocol; ?>
                            </div>
                        <?php endif; ?>

                        <?php
                        $port = $tc->port ? $tc->port : $tc->target->port;

                        if ($port) {
                            $port = ":" . $port;
                        }
                        ?>
                        <div class="target">
                            <a href="<?= $this->createUrl("project/target", ["id" => $project->id, "target" => $tc->target_id]) ?>"><?= $tc->target->host; ?><?= $port; ?></a>
                        </div>

                        <div class="attachments">
                            <?= Yii::t("app", "Attachments"); ?>:
                            <?= count($tc->attachments) ?>
                        </div>
                    </div>

                    <a class="pull-right" href="#edit-evidence" onclick="admin.issue.evidence.toggleEvidenceEditBlock('result', <?= $evidence->id; ?>)"><i class="icon-pencil"></i></a>

                    <div class="field-block result-field evidence-field">
                        <b><?= Yii::t("app", "Result") ?></b>
                        <br/>

                        <div class="field-value issue-pre"><?= $tc->result ? Utils::getFirstWords($tc->result, Yii::app()->params["issue.field_length"]) : "-"; ?></div>
                        <br/>
                    </div>

                    <div class="field-block hidden evidence-field result-textarea">
                        <b><?= Yii::t("app", "Result") ?></b>
                        <br/>
                        <textarea class="textarea textarea-evidence-view" rows="3" name="text"><?= CHtml::encode($tc->result); ?></textarea>
                        <br/>
                        <button class="btn pull-right hidden update-evidence" onclick="admin.issue.evidence.update(<?= $evidence->id; ?>)">
                            <?php echo Yii::t("app", "Update"); ?>
                        </button>&nbsp;
                        <div class="clearfix"></div>
                    </div>

                    <a class="pull-right" href="#edit-poc" onclick="admin.issue.evidence.toggleEvidenceEditBlock('poc', <?= $evidence->id; ?>)"><i class="icon icon-pencil"></i></a>

                    <div class="field-block poc-field evidence-field">
                        <b><?= Yii::t("app", "PoC") ?></b>
                        <br/>

                        <div class="field-value issue-pre"><?= $tc->poc ? Utils::getFirstWords($tc->poc, Yii::app()->params["issue.field_length"]) : "-"; ?></div>
                    </div>

                    <div class="field-block evidence-field poc-textarea hidden">
                        <b><?= Yii::t("app", "PoC") ?></b>
                        <br/>
                        <textarea class="textarea textarea-evidence-view" rows="3" name="text" ><?= CHtml::encode($tc->poc); ?></textarea>
                        <br/>
                        <button class="btn pull-right hidden update-evidence" onclick="admin.issue.evidence.update(<?= $evidence->id; ?>)">
                            <?php echo Yii::t("app", "Update"); ?>
                        </button>&nbsp;
                        <div class="clearfix"></div>
                    </div>

                    <div class="row">
                        <div class="span6">
                            <div class="issue-button-group issue-rating-selector">
                                <select class="elem-style pull-right solution-selector"
                                    onchange="admin.issue.evidence.update(<?= $evidence->id; ?>)">
                                    <option value=""><?= Yii::t("app", "N/A"); ?></option>
                                    <?php foreach ($solutions as $solution): ?>
                                        <option value="<?= $solution->id ?>" <?php if ($tc->solutions && $tc->solutions[0]->check_solution_id == $solution->id) echo "selected"; ?>><?= $solution->localizedTitle ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="field-block">
                                <b><?= Yii::t("app", "Solution") ?></b>
                                <br/>
                                <div class="field-value">
                                    <?php if (!count($tc->solutions) && !$tc->solution && !$tc->solutionTitle): ?>
                                        <i class="icon icon-minus"></i>
                                    <?php elseif (count($tc->solutions)): ?>
                                        <?php foreach ($tc->solutions as $solution): ?>
                                            <div>
                                                <div class="solution-title"><?= $solution->solution->getLocalizedTitle(); ?></div>
                                                <div class="solution-text">
                                                    <?= Utils::getFirstWords($solution->solution->getLocalizedSolution(), Yii::app()->params["issue.field_length"]); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?= $tc->solutionTitle ?><br>
                                        <?= Utils::getFirstWords($tc->solution, Yii::app()->params["issue.field_length"]); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>
                    <br/>

                    <div class="row">
                        <div class="span6">
                            <div class="issue-button-group issue-rating-selector">
                                <select class="elem-style pull-right rating-selector"
                                    onchange="admin.issue.evidence.update(<?= $evidence->id; ?>)">
                                    <?php foreach ($ratings as $rating): ?>
                                        <option value="<?= $rating ?>" <?php if ($rating == $tc->rating) echo "selected"; ?>><?= TargetCheck::getRatingNames()[$rating] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="field-block">
                                <b><?= Yii::t("app", "Rating") ?></b>
                                <br/>
                                <span class="rating">
                                    <?php echo $this->renderPartial("partial/check-rating", ["check" => $tc]); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $i++; ?>
        <?php endforeach; ?>
    </div>
</div>
<script>
    $(".target-tabs a").click(function (e) {
        e.preventDefault();
        $(this).tab("show");
    });
</script>
