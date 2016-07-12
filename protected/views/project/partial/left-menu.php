<div id="left-menu" class="span4 info-block">
    <a href="<?= $this->createUrl("project/issues", ["id" => $project->id]); ?>"><h3><?= Yii::t("app", "Summary Of Issues") ?></h3></a>
    <br/>
    <div id="project-quick-nav-issues-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#project-quick-nav-issues');"><i class="icon-chevron-up"></i></div>
    <h3><a href="#toggle" onclick="system.toggleBlock('#project-quick-nav-issues');"><?php echo Yii::t("app", "Issues"); ?></a></h3>

    <div id="project-quick-nav-issues">
        <?php  if (count($project->issues)): ?>
            <?php foreach ($project->issues as $issue): ?>
                <div class="project-quick-nav-issue">
                    <div class="issue">
                        <?php
                            $ratingLabels = [
                                TargetCheck::RATING_LOW_RISK => "low",
                                TargetCheck::RATING_MED_RISK => "med",
                                TargetCheck::RATING_HIGH_RISK => "high"
                            ];
                        ?>
                        <div style="margin-right: 10px;" class="marker label-<?= in_array($issue->highestRating, array_keys($ratingLabels)) ? $ratingLabels[$issue->highestRating] : "no" ?>-risk"></div>
                        <a href="<?php echo $this->createUrl("project/issue", ["id" => $project->id, "issue" => $issue->id]); ?>"><?php echo CHtml::encode($issue->check->name); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <?= Yii::t("app", "No issues yet.") ?>
        <?php endif; ?>
        <div class="clearfix"></div>
    </div>
</div>
