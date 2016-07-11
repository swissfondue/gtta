<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?= $this->createUrl("project/issue", ["id" => $project->id, "issue" => $issue->id]); ?>"><?= Yii::t("app", "View"); ?></a></li>
            <li><a href="<?= $this->createUrl("project/editissue", ["id" => $project->id, "issue" => $issue->id]); ?>"><?= Yii::t("app", "Edit"); ?></a></li>
        </ul>
    </div>

    <h1><?= CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <div id="issue-information">
                <div class="field-block">
                    <h3><?= Yii::t("app", "Title") ?></h3>
                    <?= $issue->check->name ?>
                </div>
                <div class="field-block">
                    <h3><?= Yii::t("app", "Background Info") ?></h3>
                    <?= $issue->check->backgroundInfo ?>
                </div>
                <div class="field-block">
                    <h3><?= Yii::t("app", "Hints") ?></h3>
                    <?= $issue->check->hints ?>
                </div>
                <div class="field-block">
                    <h3><?= Yii::t("app", "Question") ?></h3>
                    <?= $issue->check->question ?>
                </div>
                <div class="field-block">
                    <h3><?= Yii::t("app", "Reference") ?></h3>
                    <?= $issue->check->_reference->name ?>
                    <br>
                    <a href="<?= $issue->check->_reference->url ?>"><?= $issue->check->_reference->url ?></a>
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
                                <li><b><a href="#"><?= sprintf("%s (%d)", $ip, count($targets)); ?></a></b></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="span6">
                    <?php foreach ($evidenceGroups as $ip => $targets): ?>
                        <div class="target-group" style="margin-bottom: 20px;">
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

                                    <div class="tab-pane <?= $i == 0 ? 'active' : '' ?>" id="evidence_<?= $target->id ?>">
                                        <div class="control-group">
                                            <span style="font-size: 16px; margin-left: 5px;"><?= Yii::t("app", "Evidence for this instance") ?></span>&nbsp;—&nbsp;
                                            <a href="<?= $this->createUrl("project/editevidence", ["id" => $project->id, "issue" => $issue->id, "evidence" => $issue->getEvidence($tc->id)->id]) ?>"><?= Yii::t("app", "Edit") ?></a>
                                            <a class="red" href="<?= $this->createUrl("project/controlevidence", ["id" => $project->id, "issue" => $issue->id, "evidence" => $issue->getEvidence($tc->id)->id]) ?>"><?= Yii::t("app", "Delete") ?></a>
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
                                                        <?= $tc->port ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= $this->createUrl("project/target", ["id" => $project->id, "target" => $target->id]) ?>"><?= $target->host ?></a>
                                                    </td>
                                                    <td>
                                                        <?= count($tc->attachments) ?>
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
                                                <div class="field-block">
                                                    <b><?= Yii::t("app", "PoC") ?></b>
                                                    <br/>
                                                    <?= $tc->poc ?>
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
                admin.issue.loadTargets('<?= $this->createUrl("project/searchtargets", ["issue" => $issue->id]) ?>', $(this).val())
            }
        });
    });
</script>