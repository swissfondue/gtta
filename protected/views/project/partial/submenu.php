<ul class="nav nav-pills">
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
        </ul>
    </li>
</ul>