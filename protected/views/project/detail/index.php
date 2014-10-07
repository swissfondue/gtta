<div class="active-header">
    <div class="pull-right">
        <?php echo $this->renderPartial('partial/submenu', array( 'page' => 'details', 'project' => $project )); ?>
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

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'project/details', 'params' => array('id' => $project->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No details yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
