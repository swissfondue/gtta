<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl('project/view', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                <li><a href="<?php echo $this->createUrl('project/edit', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('project/users', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Users'); ?></a></li>
            <?php endif; ?>
            <?php if (User::checkRole(User::ROLE_ADMIN) || User::checkRole(User::ROLE_CLIENT)): ?>
                <li class="active"><a href="<?php echo $this->createUrl('project/details', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Details'); ?></a></li>
            <?php endif; ?>
            <li><a href="<?php echo $this->createUrl('project/vulns', array( 'id' => $project->id )); ?>"><?php echo Yii::t('app', 'Vulns'); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('project/editdetail', array( 'id' => $project->id )); ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Detail'); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($details) > 0): ?>
                <table class="table detail-list">
                    <tbody>
                        <tr>
                            <th class="detail"><?php echo Yii::t('app', 'Detail'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($details as $detail): ?>
                            <tr data-id="<?php echo $detail->id; ?>" data-control-url="<?php echo $this->createUrl('project/controldetail'); ?>">
                                <td class="detail">
                                    <a href="<?php echo $this->createUrl('project/editdetail', array( 'id' => $project->id, 'detail' => $detail->id )); ?>"><?php echo CHtml::encode($detail->subject); ?></a>
                                    <div class="content">
                                        <?php echo CHtml::encode($detail->content); ?>
                                    </div>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $detail->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($p->pageCount > 1): ?>
                    <div class="pagination">
                        <ul>
                            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('project/details', array( 'id' => $project->id, 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                                <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                    <a href="<?php echo $this->createUrl('project/details', array( 'id' => $project->id, 'page' => $i )); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('project/details', array( 'id' => $project->id, 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No details yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
