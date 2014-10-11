<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($projects) > 0): ?>
                <div>
                    <table class="table project-header">
                        <tbody>
                            <tr>
                                <th class="name"><?php echo Yii::t("app", "Project"); ?></th>
                                <th class="hours"><?php echo Yii::t("app", "Allocated"); ?></th>
                                <th class="hours"><?php echo Yii::t("app", "Spent"); ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php foreach ($projects as $project): ?>
                    <div id="project-<?php echo $project->id; ?>" class="project-header" data-id="<?php echo $project->id; ?>">
                        <table class="table project-header">
                            <tbody>
                                <tr>
                                    <td class="name">
                                        <a href="#project-<?php echo $project->id; ?>" onclick="admin.timeTracker.toggleProject(<?php echo $project->id; ?>);"><?php echo CHtml::encode($project->name); ?></a>
                                    </td>
                                    <td class="hours">
                                        <?php
                                            $allocated = $project->hours_allocated;
                                            echo sprintf("%.1f h", $allocated ? $allocated : 0.0);
                                        ?>
                                    </td>
                                    <td class="hours">
                                        <?php
                                            $spent = $project->userHoursSpent;
                                            echo sprintf("%.1f h", $spent ? $spent : 0.0);
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="project-body hide" data-id="<?php echo $project->id; ?>">
                        <?php foreach ($project->projectUsers as $user): ?>
                            <div class="user">
                                <table class="user">
                                    <tbody>
                                        <tr>
                                            <td class="name">
                                                <?php echo $user->user->name ? CHtml::encode($user->user->name) : $user->user->email; ?>
                                            </td>
                                            <td class="hours">
                                                <?php echo $user->hours_allocated; ?> h
                                            </td>
                                            <td class="hours">
                                                <?php
                                                    $spent = $user->hoursSpent;
                                                    echo sprintf("%.1f h", $spent ? $spent : 0.0);
                                                ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php echo Yii::t("app", "No projects to display."); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
