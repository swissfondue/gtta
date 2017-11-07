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
                 data-update-evidence-url-<?= $tc->id ?>="<?php echo $this->createUrl("project/updateevidence", ["id" => $project->id, "issue" => $issue->id,"targetCheck" => $tc->id]) ?> ">
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

                    <div class="field-block">
                        <b><?= Yii::t("app", "Result") ?></b>
                        <br/>
                        <textarea id="result-<?= $tc->id ?>" class="textarea" rows="3" name="text"><?= $tc->result ?></textarea>
                        <br/>
                    </div>

                    <div class="field-block evidence-field poc">
                        <b><?= Yii::t("app", "PoC") ?></b>
                        <br/>
                        <textarea id="poc-<?= $tc->id ?>" class="textarea" rows="3" name="text" ><?= $tc->poc ?></textarea>
                        <br/>
                    </div>
                    <div class="row">
                        <div class="span6">
                            <div class="field-block">
                                <b><?= Yii::t("app", "Solution") ?></b>
                                <br/>
                                <div class="field-value">
                                    <?php if (!count($tc->solutions) && !$tc->solution && !$tc->solutionTitle): ?>
                                        <i class="icon icon-minus"></i>
                                    <?php elseif (count($tc->solutions)): ?>
                                        <?php foreach ($tc->solutions as $solution): ?>
                                            <div>
                                                <?= $solution->solution->title ?><br>
                                                <?= Utils::getFirstWords($solution->solution->solution, Yii::app()->params["issue.field_length"]); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?= $tc->solutionTitle ?><br>
                                        <?= Utils::getFirstWords($tc->solution, Yii::app()->params["issue.field_length"]); ?>
                                    <?php endif; ?>
                                </div>


                            </div>
                        </div>
                        <div class="span6">
                            <select id="solution-<?= $tc->id ?>" class="elem-style pull-right">
                                <option selected="selected" value="" disabled><?php echo Yii::t("app", "Select Solution"); ?></option>
                                <?php foreach ($solutions as $solution): ?>
                                    <option value="<?= $solution->id ?>"><?= $solution->localizedTitle ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <br/>
                    <div class="row">
                        <div class="span6">
                            <div class="field-block">
                                <b><?= Yii::t("app", "Rating") ?></b>
                                <br/>
                                <?php echo $this->renderPartial("partial/check-rating", ["check" => $tc]); ?>
                            </div>
                        </div>
                        <div class="span6">
                            <select id="rating-<?= $tc->id ?>" class="elem-style pull-right">
                                <option selected="selected" value="" disabled><?php echo Yii::t("app", "Select Rating"); ?></option>
                                <?php foreach ($ratings as $rating): ?>
                                    <option value="<?= $rating ?>"><?= TargetCheck::getRatingNames()[$rating] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                </div>

                <hr>
                <button class="btn pull-right"
                        onclick="admin.issue.evidence.update(<?= $tc->id ?>)"><?php echo Yii::t("app", "Update"); ?></button>&nbsp;
            </div>

            <?php $i++; ?>
        <?php endforeach; ?>
    </div>
</div>
<style>
    .textarea {
        width:450px;
    }
</style>
<script>
    $(".target-tabs a").click(function (e) {
        e.preventDefault();
        $(this).tab("show");
    });
</script>
