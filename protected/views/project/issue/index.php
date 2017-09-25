<div class="active-header">
    <div class="pull-right">
        <?= $this->renderPartial("partial/submenu", ["page" => "issues", "project" => $project]); ?>
    </div>

    <div class="pull-right buttons">
        <?php if (User::checkRole(User::ROLE_USER)): ?>
            <div class="btn-group">
                <a class="btn" href="#" onclick="admin.showCheckSearchPopup();">
                    <i class="icon icon-plus"></i>
                    <?php echo Yii::t("app", "New"); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <h1><?= CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div
    class="container"
    data-add-issue-url="<?= $this->createUrl("project/addIssue", ["id" => $project->id]); ?>">
    <div class="row">
        <div class="span8">
            <?php if (count($issues) > 0): ?>
                <table class="table issue-list">
                    <tbody>
                        <tr>
                            <th><?= Yii::t("app", "Name"); ?></th>
                            <th class="status"><?= Yii::t("app", "Status"); ?></th>
                            <th class="status"><?= Yii::t("app", "Affects"); ?></th>
                            <?php if (User::checkRole(User::ROLE_USER)): ?>
                                <th>&nbsp;</th>
                            <?php endif; ?>
                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <th class="actions">&nbsp;</th>
                            <?php endif; ?>
                        </tr>
                        <?php foreach ($issues as $issue): ?>
                            <tr data-id="<?= $issue->id; ?>" data-control-url="<?= $this->createUrl("project/controlissue"); ?>">
                                <td class="name">
                                    <a href="<?= $this->createUrl("project/issue", ["id" => $project->id, "issue" => $issue->id]); ?>">
                                        <?= CHtml::encode($issue->name); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                        switch ($issue->top_rating) {
                                            case TargetCheck::RATING_HIGH_RISK:
                                                $label = Yii::t("app", "High");
                                                $ratingClass = "label-high-risk";

                                                break;

                                            case TargetCheck::RATING_MED_RISK:
                                                $label = Yii::t("app", "Medium");
                                                $ratingClass = "label-med-risk";

                                                break;

                                            case TargetCheck::RATING_LOW_RISK:
                                                $label = Yii::t("app", "Low");
                                                $ratingClass = "label-low-risk";

                                                break;

                                            case TargetCheck::RATING_INFO:
                                                $ratingClass = "label-info";
                                                $label = Yii::t("app", "Info");

                                                break;

                                            case TargetCheck::RATING_NONE:
                                                $label = Yii::t("app", "None");
                                                $ratingClass = "";

                                                break;

                                            case TargetCheck::RATING_HIDDEN:
                                                $label = Yii::t("app", "Hidden");
                                                $ratingClass = "";

                                                break;

                                            case TargetCheck::RATING_NO_VULNERABILITY:
                                                $label = Yii::t("app", "No Vulnerability");
                                                $ratingClass = "";

                                                break;

                                            default:
                                                break;
                                        }
                                    ?>
                                    <span class="label <?= $ratingClass ?>"><?= $label ?></span>
                                </td>
                                <td>
                                    <?= $issue->affected_targets; ?>
                                </td>
                                <td>
                                    <?php if(isset($issue->not_filled_ev)): ?>
                                        <a style="color:red;" href="<?= $this->createUrl("project/issue", ["id" => $project->id, "issue" => $issue->id, "evidence" => $issue->not_filled_ev]); ?>">
                                            !
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                    <td class="actions">
                                        <a href="#del" title="<?= Yii::t("app", "Delete"); ?>" onclick="system.control.del(<?= $issue->id; ?>);"><i class="icon icon-remove"></i></a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?= $this->renderPartial("/layouts/partial/pagination", ["p" => $p, "url" => "project/issues", "params" => ["id" => $project->id]]); ?>
            <?php else: ?>
                <?= Yii::t("app", "No issues yet."); ?>
            <?php endif; ?>
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

<?= $this->renderPartial("/layouts/partial/check-selector", ["project" => $project]); ?>