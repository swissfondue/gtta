<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl('gt/viewtype', array('id' => $category->id, 'type' => $type->id)); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('gt/edittype', array('id' => $category->id, 'type' => $type->id)); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('gt/editmodule', array('id' => $category->id, 'type' => $type->id)) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Module'); ?></a>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($modules) > 0): ?>
                <table class="table category-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Module'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($modules as $module): ?>
                            <tr data-id="<?php echo $module->id; ?>" data-control-url="<?php echo $this->createUrl('gt/controlmodule'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('gt/viewmodule', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id)); ?>"><?php echo CHtml::encode($module->localizedName); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $module->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'gt/viewtype', 'params' => array('id' => $category->id, 'type' => $type->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No modules yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
