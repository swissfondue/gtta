<?php if ($notFilledHost):  ?>
    <script>
        $(window).load(function(){
           var notFilledHost =  <?php echo json_encode(Utils::getFirstWords($notFilledHost,1,true))?>;
           var evId =  <?php echo json_encode($notFilledEv)?>;
           $('#simple-link-'+notFilledHost).click();
            var checkExist = setInterval(function() {
                if ($('#evidence_tab_'+evId).length) {
                    clearInterval(checkExist);
                    $('li').removeClass('active');
                    $('#evidence_tab_'+evId).addClass ('active');
                    $(".tab-pane").removeClass('active');
                    $('#evidence_'+evId).addClass ('active');
                    $('html, body').animate({
                        scrollTop: $('#evidence_tab_'+evId).offset().top
                    }, 1000);
                }
            }, 100); // check every 100ms
        });
    </script>
<?php endif; ?>
<style>
    .elem-style {
        width: 80%;
    }
    .button-group-padding {
        padding-bottom: 20px;
    }
</style>
<div class="active-header">
    <h1><?= CHtml::encode($this->pageTitle); ?></h1>
</div>
<hr>
<div
        class="container"
        id=""
        data-get-running-checks-url="<?= $this->createUrl("project/issuerunningchecks", ["id" => $project->id, "issue" => $issue->id]); ?>"
        data-update-running-checks-url="<?php echo $this->createUrl("project/updateissuechecks", ["id" => $project->id, "issue" => $issue->id]); ?>"
        data-add-evidence-url="<?= $this->createUrl("project/addEvidence", ["id" => $project->id, "issue" => $issue->id]); ?>">
    <div class="row">
        <div class="span8">
            <div id="issue-information">
                <span style="font-size: 20px;"><?= Yii::t("app", "Issue Information") ?></span>&nbsp;—&nbsp;
                <a href="<?= $this->createUrl("check/editcheck", ["id" => $check->control->check_category_id, "control" => $check->check_control_id, "check" => $check->id]) ?>"><?= Yii::t("app", "Edit") ?></a>
                <a class="red" href="#"
                   onclick="admin.issue.delete('<?= $this->createUrl("project/controlissue") ?>', <?= $issue->id ?>, function () { system.redirect('<?= $this->createUrl("project/issues", ["id" => $project->id]) ?>') })"><?= Yii::t("app", "Delete") ?></a>
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

            <?php if (User::checkRole(User::ROLE_USER)): ?>
            <div data-type="evidence"
                 data-update-evidence-url="<?php echo $this->createUrl("project/updateevidence", ["id" => $project->id, "issue" => $issue->id]); ?>">
                <div class="span8 button-group-padding">
                    <div class="row">
                        <div class="span4">
                            <select id="ratingAll" class="elem-style pull-right">
                                <option selected="selected" value="" disabled>Select Rating</option>
                                <?php foreach ($ratings as $rating): ?>
                                    <option value="<?= $rating ?>"><?= TargetCheck::getRatingNames()[$rating] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="span4 ">
                            <button class="btn elem-style"
                                    onclick="admin.issue.evidence.updateAll(true)"><?php echo Yii::t("app", "Update all Evidences"); ?></button>&nbsp;
                        </div>
                    </div>
                </div>
                <div class="span8 button-group-padding">
                    <div class="row">
                        <div class="span4">
                            <select id="solutionAll" class="elem-style pull-right">
                                <option selected="selected" value="" disabled>Select Solution</option>
                                <?php foreach ($solutions as $solution): ?>
                                    <option value="<?= $solution->id ?>"><?= $solution->localizedTitle ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="span4">
                            <button class="btn elem-style"
                                    onclick="admin.issue.evidence.updateAll(false,true)"><?php echo Yii::t("app", "Update all Evidences"); ?></button>&nbsp;
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <br>
                <br>
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
                                    <!--                                                                <li><b><a href="--><?php //echo CController::createUrl('project/evidenceview', array('host' => $host, 'issue' => $issue->id, 'project' => $project->id)) ?><!--">--><?php //echo sprintf("%s (%d)", $host, count($evidences)) ?><!--</a></b></li>-->

                                    <li>
                                        <b><?php echo CHtml::ajaxLink(
                                                sprintf("%s (%d)", $host, count($evidences)), CController::createUrl('project/evidenceview', ['host' => $host, 'issue' => $issue->id, 'project' => $project->id]),
                                                ['update' => '#simple-div'],
                                                ['id' => 'simple-link-' . Utils::getFirstWords($host,1, true), 'class' => 'evidence-link', 'name' => $host]
                                            ); ?></b></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="span6">
                        <div id="simple-div"></div>

                    </div>
                </div>
            </div>

        </div>

        <div class="span4">
            <?php
            echo $this->renderPartial(
                "partial/right-block", [
                "quickTargets" => $quickTargets,
                "project" => $project,
                "category" => null,
                "target" => null
            ]
            );
            ?>
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
                       type="text"/>
                <ul class="table target-list"></ul>
                <span class="no-search-result" style="display:none"><?= Yii::t("app", "No targets found.") ?></span>
            </div>
        </div>
    </div>
</div>

<script>

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