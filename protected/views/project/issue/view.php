<div class="active-header">
    <h1><?= CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div
    class="container"
    data-get-running-checks-url="<?= $this->createUrl("project/issuerunningchecks", ["id" => $project->id, "issue" => $issue->id]); ?>"
    data-update-running-checks-url="<?php echo $this->createUrl("project/updateissuechecks", ["id" => $project->id, "issue" => $issue->id]); ?>"
    data-add-evidence-url="<?= $this->createUrl("project/addEvidence", ["id" => $project->id, "issue" => $issue->id]); ?>">
    <div class="row">
        <div class="span8">
            <div id="issue-information">
                <span style="font-size: 20px;"><?= Yii::t("app", "Issue Information") ?></span>&nbsp;—&nbsp;
                <a href="<?= $this->createUrl("check/editcheck", ["id" => $check->control->check_category_id, "control" => $check->check_control_id, "check" => $check->id]) ?>"><?= Yii::t("app", "Edit") ?></a>
                <a class="red" href="#" onclick="admin.issue.delete('<?= $this->createUrl("project/controlissue") ?>', <?= $issue->id ?>, function () { system.redirect('<?= $this->createUrl("project/issues", ["id" => $project->id]) ?>') })"><?= Yii::t("app", "Delete") ?></a>
                <br/><br/>
                <div class="field-block">
                    <h3><?= Yii::t("app", "Title") ?></h3>
                    <?= $check->getLocalizedName() ? $check->getLocalizedName() : Yii::t("app", "N/A") ?>
                </div>
                <div class="field-block">
                    <h3><?= Yii::t("app", "Background Info") ?></h3>
                    <?= $check->backgroundInfo ? $check->backgroundInfo : Yii::t("app", "N/A") ?>
                </div>
                <div class="field-block">
                    <h3><?= Yii::t("app", "Hints") ?></h3>
                    <?= $check->hints ? $check->hints : Yii::t("app", "N/A") ?>
                </div>
                <div class="field-block">
                    <h3><?= Yii::t("app", "Question") ?></h3>
                    <?= $check->question ? $check->question : Yii::t("app", "N/A") ?>
                </div>
                <div class="field-block">
                    <h3><?= Yii::t("app", "Reference") ?></h3>
                    <?php if ($check->_reference->name): ?>
                        <?= $check->_reference->name ?>
                        <br>
                        <a href="<?= $check->_reference->url ?>"><?= $check->_reference->url ?></a>
                    <?php else: ?>
                        <?php Yii::t("app", "N/A") ?>
                    <?php endif; ?>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="span8">
                    <b style="font-size: 20px"><?= Yii::t("app", "Assets affected by this Issue") ?></b>
                    &nbsp;—&nbsp;
                    <a href="#" onclick="admin.issue.showTargetSelectDialog();"><?= Yii::t("app", "Add") ?></a>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="span2">
                    <div class="target-ip-list">
                        <ul class="clear-ul">
                            <?php foreach ($evidenceGroups as $host => $evidences): ?>
                                <li><b><a href="#<?= $host ?>"><?= sprintf("%s (%d)", $host, count($evidences)); ?></a></b></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="span6">
                    <?php foreach ($evidenceGroups as $host => $evidences): ?>
                        <div id="<?= $host ?>" class="target-group" style="margin-bottom: 20px;">
                            <h3><?= Yii::t("app", "Evidences for {ip}", ["{ip}" => $host]) ?></h3>

                            <ul class="nav nav-tabs target-tabs">
                                <?php $i = 0; ?>
                                <?php foreach ($evidences as $evidence): ?>
                                    <li class="<?= $i == 0 ? "active" : "" ?>">
                                        <a href="#evidence_<?= $evidence->id; ?>">#<?= $i; ?></a>
                                    </li>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </ul>

                            <div class="tab-content">
                                <?php $i = 0; ?>

                                <?php foreach ($evidences as $evidence): ?>
                                    <?php $tc = $evidence->targetCheck; ?>

                                    <div class="tab-pane <?= $i == 0 ? "active" : "" ?>" id="evidence_<?= $evidence->id; ?>" data-target-check-id="<?= $tc->id ?>">
                                        <div class="control-group">
                                            <span style="font-size: 16px; margin-left: 5px;"><?= Yii::t("app", "Evidence for this instance") ?></span>&nbsp;—&nbsp;
                                            <a href="<?= $this->createUrl("project/evidence", ["id" => $project->id, "issue" => $issue->id, "evidence" => $evidence->id]) ?>"><?= Yii::t("app", "Edit") ?></a>
                                            <a class="red" href="#" onclick="admin.issue.evidence.delete('<?= $this->createUrl("project/controlevidence") ?>', <?= $evidence->id ?>, function () { system.redirect('<?= $this->createUrl("project/issue", ["id" => $project->id, "issue" => $issue->id]) ?>') });"><?= Yii::t("app", "Delete") ?></a>

                                            <?php if ($tc->check->automated): ?>
                                                <div style="float:right">
                                                    <div class="run-info inline">
                                                        <div class="check-time"></div>
                                                    </div>
                                                    <div class="run-buttons inline">
                                                        <a href="#start" title="<?php echo Yii::t("app", "Start"); ?>" class="start-button <?= $tc->isRunning ? "hide" : "" ?>" onclick="admin.issue.evidence.start('<?= $this->createUrl("project/controlcheck", ["id" => $project->id, "target" => $tc->target_id, "category" => $tc->check->control->check_category_id, "check" => $tc->id]); ?>', <?= $tc->id; ?>);"><i class="icon icon-play"></i></a>
                                                        <a href="#stop" title="<?php echo Yii::t("app", "Stop"); ?>" class="stop-button <?= !$tc->isRunning ? "hide" : "" ?>" onclick="admin.issue.evidence.stop('<?= $this->createUrl("project/controlcheck", ["id" => $project->id, "target" => $tc->target_id, "category" => $tc->check->control->check_category_id, "check" => $tc->id]); ?>', <?php echo $tc->id; ?>);"><i class="icon icon-stop"></i></a>
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

                                                <?php if ($tc->result): ?>
                                                    <div class="field-value issue-pre"><?= Utils::getFirstWords($tc->result, Yii::app()->params["issue.field_length"]); ?></div>
                                                <?php else: ?>
                                                    <div class="field-value"><i class="icon icon-minus"></i></div>
                                                <?php endif; ?>
                                                <br/>
                                            </div>

                                            <div class="field-block evidence-field poc">
                                                <b><?= Yii::t("app", "PoC") ?></b>
                                                <br/>

                                                <?php if ($tc->poc): ?>
                                                    <div class="field-value issue-pre"><?= Utils::getFirstWords($tc->poc, Yii::app()->params["issue.field_length"]); ?></div>
                                                <?php else: ?>
                                                    <div class="field-value"><i class="icon icon-minus"></i></div>
                                                <?php endif; ?>
                                                <br/>
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

                                            <br/>

                                            <div class="field-block">
                                                <b><?= Yii::t("app", "Rating") ?></b>
                                                <br/>
                                                <?php echo $this->renderPartial("partial/check-rating", ["check" => $tc]); ?>
                                            </div>
                                        </div>

                                        <hr>
                                    </div>

                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="span4">
            <?php
                echo $this->renderPartial("partial/right-block", array(
                    "quickTargets" => $quickTargets,
                    "project" => $project,
                    "category" => null,
                    "target" => null
                ));
            ?>
        </div>
    </div>
</div>

<div
    class="modal fade"
    id="target-select-dialog"
    tabindex="-1"
    role="dialog"
    aria-labelledby="smallModal"
    aria-hidden="true"
    data-search-target-url="<?= $this->createUrl("project/searchTargets", ["id" => $project->id, "issue" => $issue->id]) ?>">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3><?= Yii::t("app", "Select Target") ?></h3>
            </div>
            <div class="modal-body">
                <input class="target-search-query"
                       placeholder="<?= Yii::t("app", "Hostname or IP address") ?>"
                       type="text" />
                <ul class="table target-list"></ul>
                <span class="no-search-result" style="display:none"><?= Yii::t("app", "No targets found.") ?></span>
            </div>
        </div>
    </div>
</div>

<script>
    $(".target-tabs a").click(function (e) {
        e.preventDefault();
        $(this).tab("show");
    });

    $(function () {
        $("#target-select-dialog input.target-search-query").keyup(function (e) {
            // if alpha or backspace
            if (/[a-zA-Z0-9_ -]/.test(String.fromCharCode(e.keyCode)) || e.keyCode == 8) {
                admin.issue.searchTargets($(this).val());
            }
        });

        setInterval(function () {
            admin.issue.getRunningChecks();
        }, 5000);

        setInterval(function () {
            admin.issue.update();
        }, 1000);

        admin.issue.initTargetSelectDialog();
    });
</script>