<ul class="nav nav-pills">
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <?= Yii::t("app", "New"); ?>
            <b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
            <li><a href="<?= $this->createUrl("project/edittarget", array("id" => $project->id )); ?>"><?= Yii::t("app", "Single Target") ?></a></li>
            <li><a href="<?= $this->createUrl("project/addtargetlist", array("id" => $project->id )); ?>"><?= Yii::t("app", "Target List") ?></a></li>
            <li><a href="<?= $this->createUrl("project/importtarget", array("id" => $project->id )); ?>"><?= Yii::t("app", "Import Targets From File") ?></a></li>
            <li class="divider"></li>
            <li><a href="#" onclick="admin.issue.showIssueAddPopup()"><?= Yii::t("app", "Issue") ?></a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <?= Yii::t("app", "Project"); ?>
            <b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
            <li <?php if ($page == "view") echo "class=\"active\"" ?>><a href="<?php echo $this->createUrl("project/view", array( "id" => $project->id )); ?>"><?php echo Yii::t("app", "View"); ?></a></li>

            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                <li <?php if ($page == "edit") echo "class=\"active\"" ?>><a href="<?php echo $this->createUrl("project/edit", array( "id" => $project->id )); ?>"><?php echo Yii::t("app", "Edit"); ?></a></li>
                <li <?php if ($page == "users") echo "class=\"active\"" ?>><a href="<?php echo $this->createUrl("project/users", array( "id" => $project->id )); ?>"><?php echo Yii::t("app", "Users"); ?></a></li>
            <?php endif; ?>

            <?php if (User::checkRole(User::ROLE_ADMIN) || User::checkRole(User::ROLE_CLIENT)): ?>
                <li <?php if ($page == "details") echo "class=\"active\"" ?>><a href="<?php echo $this->createUrl("project/details", array( "id" => $project->id )); ?>"><?php echo Yii::t("app", "Details"); ?></a></li>
            <?php endif; ?>

            <?php if (User::checkRole(User::ROLE_USER)): ?>
                <li <?php if ($page == "time") echo "class=\"active\"" ?>><a href="<?php echo $this->createUrl("project/time", array( "id" => $project->id )); ?>"><?php echo Yii::t("app", "Time"); ?></a></li>
            <?php endif; ?>

            <?php if (User::checkRole(User::ROLE_USER)): ?>
                <li <?php if ($page == "issues") echo "class=\"active\"" ?>><a href="<?php echo $this->createUrl("project/issues", array( "id" => $project->id )); ?>"><?php echo Yii::t("app", "Issues"); ?></a></li>
            <?php endif; ?>

            <li <?php if ($page == "vulns") echo "class=\"active\"" ?>><a href="<?php echo $this->createUrl("vulntracker/vulns", array( "id" => $project->id )); ?>"><?php echo Yii::t("app", "Vulnerabilities"); ?></a></li>

            <li class="divider"></li>

            <?php if (!User::checkRole(User::ROLE_CLIENT) || Yii::app()->user->getShowReports()): ?>
                <li <?php if (in_array(Yii::app()->controller->action->id, ["project", "sections", "projectRtf", "projectDocx"])) echo "class=\"active\""; ?>><a href="<?php echo $this->createUrl("projectReport/project", ["id" => $project->id]); ?>"><?php echo Yii::t("app", "Report"); ?></a></li>
                <li <?php if (Yii::app()->controller->action->id == "comparison") echo "class=\"active\""; ?>><a href="<?php echo $this->createUrl("projectReport/comparison", ["id" => $project->id]); ?>"><?php echo Yii::t("app", "Comparison"); ?></a></li>
                <li <?php if (Yii::app()->controller->action->id == "vulnexport") echo "class=\"active\""; ?>><a href="<?php echo $this->createUrl("projectReport/vulnexport", ["id" => $project->id]); ?>"><?php echo Yii::t("app", "Vulnerability Export"); ?></a></li>
                <li <?php if (Yii::app()->controller->action->id == "fulfillment") echo "class=\"active\""; ?>><a href="<?php echo $this->createUrl("projectReport/fulfillment", ["id" => $project->id]); ?>"><?php echo Yii::t("app", "Degree of Fulfillment"); ?></a></li>
                <li <?php if (Yii::app()->controller->action->id == "riskmatrix") echo "class=\"active\""; ?>><a href="<?php echo $this->createUrl("projectReport/riskmatrix", ["id" => $project->id]); ?>"><?php echo Yii::t("app", "Risk Matrix"); ?></a></li>
            <?php endif; ?>
        </ul>
    </li>
</ul>

<?= $this->renderPartial("//project/issue/partial/check-selector", ["project" => $project]); ?>