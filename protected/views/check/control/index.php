<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl('check/viewcontrol', array( 'id' => $category->id, 'control' => $control->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('check/editcontrol', array( 'id' => $category->id, 'control' => $control->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
        </ul>
    </div>
    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id )) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Check'); ?></a>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
        <?php if ($count): ?>
            <span class="header-detail"><?php echo $count; ?></span>
        <?php endif; ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($checks) > 0): ?>
                <table class="table check-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Check'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($checks as $check): ?>
                            <tr data-id="<?php echo $check->id; ?>" data-control-url="<?php echo $this->createUrl('check/control/check/control'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo CHtml::encode($check->localizedName); ?></a>
                                    <?php if ($check->automated): ?>
                                        <i class="icon-cog" title="<?php echo Yii::t('app', 'Automated'); ?>"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $check->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($p->pageCount > 1): ?>
                    <div class="pagination">
                        <ul>
                            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('check/viewcontrol', array( 'id' => $category->id, 'control' => $control->id, 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                                <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                    <a href="<?php echo $this->createUrl('check/viewcontrol', array( 'id' => $category->id, 'control' => $control->id, 'page' => $i )); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('check/view', array( 'id' => $category->id, 'control' => $control->id, 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No checks yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
