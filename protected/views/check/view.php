<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl('check/view', array( 'id' => $category->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('check/edit', array( 'id' => $category->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('check/editcontrol', array( 'id' => $category->id )) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Control'); ?></a>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($controls) > 0): ?>
                <table class="table check-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Control'); ?></th>
                            <th class="check-count"><?php echo Yii::t('app', 'Checks'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($controls as $control): ?>
                            <tr data-id="<?php echo $control->id; ?>" data-control-url="<?php echo $this->createUrl('check/control/control'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('check/viewcontrol', array( 'id' => $category->id, 'control' => $control->id )); ?>"><?php echo CHtml::encode($control->localizedName); ?></a>
                                </td>
                                <td><?php echo $control->checkCount; ?></td>
                                <td class="actions">
                                    <a href="#up" title="<?php echo Yii::t('app', 'Move Up'); ?>" onclick="system.control.up(<?php echo $control->id; ?>);"><i class="icon icon-arrow-up"></i></a>
                                    <a href="#down" title="<?php echo Yii::t('app', 'Move Down'); ?>" onclick="system.control.down(<?php echo $control->id; ?>);"><i class="icon icon-arrow-down"></i></a>
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $control->id; ?>, '<?php echo Yii::t('app', 'WARNING! ALL CHECKS WITHIN THIS CONTROL WILL BE DELETED!'); ?>');"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'check/view', 'params' => array('id' => $category->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No controls yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
