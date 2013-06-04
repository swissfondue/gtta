<div class="pull-right">
    <a class="btn" href="<?php echo $this->createUrl('reference/edit') ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Reference'); ?></a>
</div>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($references) > 0): ?>
                <table class="table reference-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Reference'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($references as $reference): ?>
                            <tr data-id="<?php echo $reference->id; ?>" data-control-url="<?php echo $this->createUrl('reference/control'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('reference/edit', array( 'id' => $reference->id )); ?>"><?php echo CHtml::encode($reference->name); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $reference->id; ?>, '<?php echo Yii::t('app', 'WARNING! ALL CHECKS RELATED TO THIS REFERENCE WILL BE DELETED!'); ?>');"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'reference/index', 'params' => array())); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No references yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
