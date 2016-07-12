<div class="active-header">
    <h1><?= CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="hide" action="<?= $this->createUrl("project/controlissue", ["id" => $project->id, "issue" => $issue->id]) ?>" method="POST">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" name="operation" />
    <input type="hidden" name="id" />
</form>

<div class="container">
    <div class="row">
        <?= $this->renderPartial("partial/left-menu", ["project" => $project]) ?>
        <div class="span8">
            <div id="issue-information">
                <span style="font-size: 20px;"><?= Yii::t("app", "Issue Information") ?></span>&nbsp;—&nbsp;
                <a href="<?= $this->createUrl("check/editcheck", ["id" => $check->control->check_category_id, "control" => $check->check_control_id, "check" => $check->id]) ?>"><?= Yii::t("app", "Edit") ?></a>
                <a class="red" href="#" onclick="alert("deleting")"><?= Yii::t("app", "Delete") ?></a>
                <br/><br/>
                <div class="field-block">
                    <h3><?= Yii::t("app", "Title") ?></h3>
                    <?= $check->name ? $check->name : Yii::t("app", "N/A") ?>
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
                    <a href="#" onclick="admin.issue.showTargetAddPopup()"><?= Yii::t("app", "Add") ?></a>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="span2">
                    <div class="target-ip-list">
                        <ul class="clear-ul">
                            <?php foreach ($evidenceGroups as $ip => $targets): ?>
                                <li><b><a href="#<?= $ip ?>"><?= sprintf("%s (%d)", $ip, count($targets)); ?></a></b></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="span6">
                    <?php foreach ($evidenceGroups as $ip => $targets): ?>
                        <div id="<?= $ip ?>" class="target-group" style="margin-bottom: 20px;">
                            <h3><?= Yii::t("app", "Evidence for :ip", [":ip" => $ip]) ?></h3>

                            <ul class="nav nav-tabs target-tabs">
                                <?php $i = 0; ?>

                                <?php foreach ($targets as $target): ?>
                                    <li class="<?= $i == 0 ? 'active' : '' ?>">
                                        <a href="#evidence_<?= $target->id ?>"><?= "#$i" ?></a>
                                    </li>

                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </ul>

                            <div class="tab-content">
                                <?php $i = 0; ?>

                                <?php foreach ($targets as $target): ?>
                                    <?php $tc = $target->getCheck($issue->check_id); ?>
                                    <?php $evidence = $issue->getEvidence($tc->id); ?>

                                    <div class="tab-pane <?= $i == 0 ? 'active' : '' ?>" id="evidence_<?= $target->id ?>" data-target-check-id="<?= $tc->id ?>">
                                        <div class="control-group">
                                            <span style="font-size: 16px; margin-left: 5px;"><?= Yii::t("app", "Evidence for this instance") ?></span>&nbsp;—&nbsp;
                                            <a href="<?= $this->createUrl("project/evidence", ["id" => $project->id, "issue" => $issue->id, "evidence" => $evidence->id]) ?>"><?= Yii::t("app", "Edit") ?></a>
                                            <a class="red" href="<?= $this->createUrl("project/controlevidence", ["id" => $project->id, "issue" => $issue->id, "evidence" => $issue->getEvidence($tc->id)->id]) ?>"><?= Yii::t("app", "Delete") ?></a>

                                            <?php if ($tc->check->automated): ?>
                                                <div class="run-buttons" style="float:right">
                                                    <a href="#start" title="<?php echo Yii::t("app", "Start"); ?>" class="start-button <?= $tc->isRunning ? "hide" : "" ?>" onclick="admin.issue.evidence.start('<?= $this->createUrl("project/controlcheck", ["id" => $project->id, "target" => $tc->target_id, "category" => $tc->check->control->check_category_id, "check" => $tc->id]); ?>', <?= $tc->id; ?>);"><i class="icon icon-play"></i></a>
                                                    <a href="#stop" title="<?php echo Yii::t("app", "Stop"); ?>" class="stop-button <?= !$tc->isRunning ? "hide" : "" ?>" onclick="admin.issue.evidence.stop('<?= $this->createUrl("project/controlcheck", ["id" => $project->id, "target" => $tc->target_id, "category" => $tc->check->control->check_category_id, "check" => $tc->id]); ?>', <?php echo $tc->id; ?>);"><i class="icon icon-stop"></i></a>
                                                </div>
                                                <div class="run-info">
                                                    <div class="check-time"></div>
                                                </div>
                                            <?php endif; ?>
                                            <hr>
                                            <table class="table" style="border-top: none;">
                                                <thead>
                                                <tr>
                                                    <th><?= Yii::t("app", "Protocols") ?></th>
                                                    <th><?= Yii::t("app", "Port") ?></th>
                                                    <th><?= Yii::t("app", "Host") ?></th>
                                                    <th><?= Yii::t("app", "Attachments") ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <?= $tc->transportProtocol ?>&nbsp;
                                                        <?= $tc->applicationProtocol ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($tc->port): ?>
                                                            <?= $tc->port ?>
                                                        <?php else: ?>
                                                            <i class="icon-minus"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= $this->createUrl("project/target", ["id" => $project->id, "target" => $target->id]) ?>"><?= $target->host ?></a>
                                                    </td>
                                                    <td>
                                                        <?php if (count($tc->attachments)): ?>
                                                            <?= count($tc->attachments) ?>
                                                        <?php else: ?>
                                                            <i class="icon-minus"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <?php if ($tc->result): ?>
                                                <div class="field-block">
                                                    <b><?= Yii::t("app", "Result") ?></b>
                                                    <br/>
                                                    <?= $tc->result ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($tc->poc): ?>
                                                <div class="field-block evidence-field">
                                                    <b><?= Yii::t("app", "PoC") ?></b>
                                                    <br/>
                                                    <div class="field-value">
                                                        <?= $tc->poc ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($tc->solution): ?>
                                                <div class="field-block">
                                                    <b><?= Yii::t("app", "Solution") ?></b>
                                                    <br/>
                                                    <?= $tc->solutionTitle ?>
                                                    <br>
                                                    <?= $tc->solution ?>
                                                </div>
                                            <?php endif; ?>
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
                    "client" => $client,
                    "statuses" => $statuses,
                    "category" => null,
                    "target" => null
                ));
            ?>
        </div>
    </div>
</div>

<div class="modal fade" id="target-select-dialog" tabindex="-1" role="dialog" aria-labelledby="smallModal" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3><?= Yii::t("app", "Select Target") ?></h3>
            </div>
            <div class="modal-body">
                <input class="target-search-query"
                       placeholder="<?= Yii::t("app", "Search String (At Least 3 Symbol)...") ?>"
                       type="text" />
                <table class="table target-list"></table>
                <span class="no-search-result" style="display:none"><?= Yii::t("app", "No Target") ?></span>
            </div>
        </div>
    </div>
</div>

<script>
    $('.target-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    $(function () {
        $("#target-select-dialog input.target-search-query").keyup(function (e) {
            // if alpha or backspace
            if (/[a-zA-Z0-9_ -]/.test(String.fromCharCode(e.keyCode)) || e.keyCode == 8) {
                admin.issue.loadTargets('<?= $this->createUrl("project/searchtargets", ["id" => $project->id, "issue" => $issue->id]) ?>', $(this).val())
            }
        });

        setInterval(function () {
            user.check.getRunningChecks("<?= $this->createUrl("project/runningchecks"); ?>", <?= $target->id ?>);
        }, 1000);

        setInterval(function () {
            user.check.update("<?php echo $this->createUrl("project/updatechecks", array("id" => $project->id, "target" => $target->id, "category" => $category->check_category_id)); ?>");
        }, 1000);
    });
</script>