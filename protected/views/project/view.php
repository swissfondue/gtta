<div class="active-header">
    <div class="pull-right">
        <?php echo $this->renderPartial('partial/submenu', array( 'page' => 'view', 'project' => $project )); ?>
    </div>

    <div class="pull-right buttons">
        <?php if (User::checkRole(User::ROLE_USER)): ?>
            <div class="btn-group">
                <a class="btn" href="<?php echo $this->createUrl("project/tracktime", array("id" => $project->id)); ?>">
                    <i class="icon icon-time"></i>
                    <?php echo Yii::t("app", "Track Time"); ?>
                </a>
            </div>
            <div class="btn-group">
                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="icon icon-plus"></i>
                    <?php echo Yii::t('app', 'New Target'); ?>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $this->createUrl('project/edittarget', array( 'id' => $project->id )); ?>">Manually</a></li>
                    <li><a href="<?php echo $this->createUrl('project/importtarget', array( 'id' => $project->id )); ?>">Import From File</a></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($targets) > 0): ?>
                <table class="table target-list">
                    <tbody>
                        <tr>
                            <th class="target"><?php echo Yii::t('app', 'Target'); ?></th>
                            <th class="stats"><?php echo Yii::t('app', 'Risk Stats'); ?></th>
                            <th class="percent"><?php echo Yii::t('app', 'Completed'); ?></th>
                            <th class="check-count"><?php echo Yii::t('app', 'Checks'); ?></th>
                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <th class="actions">&nbsp;</th>
                            <?php endif; ?>
                        </tr>
                        <?php foreach ($targets as $target): ?>
                            <tr data-id="<?php echo $target->id; ?>" data-control-url="<?php echo $this->createUrl('project/controltarget'); ?>">
                                <td class="target">
                                    <?php if (User::checkRole(User::ROLE_USER) || Yii::app()->user->getShowDetails()): ?>
                                        <a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id )); ?>">
                                            <?php echo CHtml::encode($target->hostPort); ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo CHtml::encode($target->hostPort); ?>
                                    <?php endif; ?>

                                    <?php if ($target->description): ?>
                                        / <span class="description"><?php echo CHtml::encode($target->description); ?></span>
                                    <?php endif; ?>

                                    <div class="categories">
                                        <?php if ($target->categories): ?>
                                            <?php foreach ($target->categories as $category): ?>
                                                <?php
                                                    $catName = $category->localizedName;
                                                    $shortened = false;

                                                    if (mb_strlen($catName) > 45) {
                                                        $catName = mb_substr($catName, 0, 45) . "...";
                                                        $shortened = true;
                                                    }

                                                    $catName = CHtml::encode($catName);
                                                ?>
                                                <?php if (User::checkRole(User::ROLE_USER) || Yii::app()->user->getShowDetails()): ?>
                                                    <a href="<?php echo $this->createUrl('project/checks', array('id' => $project->id, 'target' => $target->id, 'category' => $category->id)); ?>"><span class="label label-target-category<?php if ($shortened) echo " shortened"; ?>"<?php if ($shortened) echo " title=\"" . CHtml::encode($category->localizedName) . "\""; ?>><?php echo $catName; ?></span></a>
                                                <?php else: ?>
                                                    <span class="label label-target-category<?php if ($shortened) echo " shortened"; ?>"<?php if ($shortened) echo " title=\"" . CHtml::encode($category->localizedName) . "\""; ?>><?php echo $catName; ?></span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <?php echo Yii::t('app', 'No categories yet.'); ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="stats">
                                    <span class="high-risk"><?php echo $target->highRiskCount ? $target->highRiskCount : 0; ?></span> /
                                    <span class="med-risk"><?php echo $target->medRiskCount ? $target->medRiskCount: 0; ?></span> /
                                    <span class="low-risk"><?php echo $target->lowRiskCount ? $target->lowRiskCount : 0; ?></span> /
                                    <span class="info"><?php echo $target->infoCount ? $target->infoCount : 0; ?></span>
                                </td>
                                <td class="percent">
                                    <?php
                                        $finished = $target->finishedCount;

                                        if (!$finished)
                                            $finished = 0;

                                        echo $target->checkCount ? sprintf('%.0f', ($finished / $target->checkCount) * 100) : '0';
                                    ?>%
                                    /
                                    <?php echo $finished; ?>
                                </td>
                                <td>
                                    <?php
                                        $checkCount = 0;

                                        foreach ($target->_categories as $category) {
                                            $checkCount += $category->check_count;
                                        }

                                        echo $checkCount;
                                    ?>
                                </td>
                                <?php if (User::checkRole(User::ROLE_USER)): ?>
                                    <td class="actions">
                                        <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $target->id; ?>);"><i class="icon icon-remove"></i></a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'project/view', 'params' => array('id' => $project->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No targets yet.'); ?>
            <?php endif; ?>
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

<script>
    $(".shortened").tooltip({
        placement:"right"
    });
</script>