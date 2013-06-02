<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl('gt/editcheck', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id, 'check' => $check->id)); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            <li class="active"><a href="<?php echo $this->createUrl('gt/dependencies', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id, 'check' => $check->id)); ?>"><?php echo Yii::t('app', 'Dependencies'); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('gt/editdependency', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id, 'check' => $check->id)) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Dependency'); ?></a>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($dependencies) > 0): ?>
                <table class="table category-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Module'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($dependencies as $dependency): ?>
                            <tr data-id="<?php echo $dependency->id; ?>" data-control-url="<?php echo $this->createUrl('gt/controldependency'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('gt/editdependency', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id, 'check' => $check->id, 'dependency' => $dependency->id)); ?>"><?php echo CHtml::encode($dependency->module->localizedName); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $dependency->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($p->pageCount > 1): ?>
                    <div class="pagination">
                        <ul>
                            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('gt/dependencies', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id, 'check' => $check->id, 'page' => $p->prevPage ? $p->prevPage : $p->page)); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                                <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                    <a href="<?php echo $this->createUrl('gt/dependencies', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id, 'check' => $check->id, 'page' => $i)); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('gt/dependencies', array('id' => $category->id, 'type' => $type->id, 'module' => $module->id, 'check' => $check->id, 'page' => $p->nextPage ? $p->nextPage : $p->page)); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No dependencies yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
