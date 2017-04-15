<?php if (User::checkRole(User::ROLE_USER)): ?>
    <div class="active-header">
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li class="active"><a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('project/edittarget', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('project/editchain', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'Check Chain'); ?></a></li>
            </ul>
        </div>

        <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
    </div>
<?php else: ?>
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
<?php endif; ?>
<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($categories) > 0): ?>
                <table class="table category-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Category'); ?></th>
                            <th class="stats"><?php echo Yii::t('app', 'Risk Stats'); ?></th>
                            <th class="percent"><?php echo Yii::t('app', 'Completed'); ?></th>
                            <th class="check-count"><?php echo Yii::t('app', 'Checks'); ?></th>
                            <?php if (User::checkRole(User::ROLE_USER)): ?>
                                <th class="actions">&nbsp;</th>
                            <?php endif; ?>
                        </tr>
                        <?php foreach ($categories as $category): ?>
                            <tr data-id="<?php echo $category->check_category_id; ?>" data-control-url="<?php echo $this->createUrl("project/controlcategory", array("id" => $project->id, "target" => $target->id)); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('project/checks', array( 'id' => $project->id, 'target' => $target->id, 'category' => $category->check_category_id )); ?>"><?php echo CHtml::encode($category->category->localizedName); ?></a>
                                </td>
                                <td class="stats">
                                    <span class="high-risk"><?php echo $category->high_risk_count; ?></span> /
                                    <span class="med-risk"><?php echo $category->med_risk_count; ?></span> /
                                    <span class="low-risk"><?php echo $category->low_risk_count; ?></span> /
                                    <span class="info"><?php echo $category->info_count; ?></span>
                                </td>
                                <td class="percent">
                                    <?php echo $category->check_count ? sprintf('%.0f', ($category->finished_count / $category->check_count) * 100) : '0'; ?>% /
                                    <?php echo $category->finished_count; ?>
                                </td>
                                <td>
                                    <?php echo $category->check_count ? $category->check_count : "0"; ?>
                                </td>
                                <?php if (User::checkRole(User::ROLE_USER)): ?>
                                    <td class="actions">
                                        <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $category->check_category_id; ?>);"><i class="icon icon-remove"></i></a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'project/target', 'params' => array('id' => $project->id, 'target' => $target->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No categories yet.'); ?>
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

<script>
    setInterval(function () {
        user.target.chain.messages('<?php print $this->createUrl('project/chainmessages', array("id" => $project->id, "target" => $target->id)); ?>');
    }, 5000);
</script>