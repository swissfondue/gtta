<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl('gt/viewmodule', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id)); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('gt/editmodule', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id)); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('gt/editcheck', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id)) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Check'); ?></a>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($checks) > 0): ?>
                <table class="table category-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Check'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($checks as $check): ?>
                            <tr data-id="<?php echo $check->id; ?>" data-control-url="<?php echo $this->createUrl('gt/controlcheck'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('gt/editcheck', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id, 'check' => $check->id)); ?>"><?php echo CHtml::encode($check->check->localizedName); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $check->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'gt/viewmodule', 'params' => array('id' => $category->id, 'type' => $type->id, 'module' => $module->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No checks yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>