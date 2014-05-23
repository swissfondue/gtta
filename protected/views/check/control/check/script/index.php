<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            <?php if ($check->automated): ?>
                <li class="active"><a href="<?php echo $this->createUrl('check/scripts', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Scripts'); ?></a></li>
            <?php endif; ?>
            <li><a href="<?php echo $this->createUrl('check/results', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Results'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('check/solutions', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Solutions'); ?></a></li>
            <li><a href="<?php echo $this->createUrl("check/share", array("id" => $category->id, "control" => $control->id, "check" => $check->id)); ?>"><?php echo Yii::t('app', "Share"); ?></a></li>
        </ul>
    </div>
    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('check/editscript', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Script'); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($scripts) > 0): ?>
                <table class="table script-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Script'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($scripts as $script): ?>
                            <tr data-id="<?php echo $script->id; ?>" data-control-url="<?php echo $this->createUrl('check/controlscript'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('check/editscript', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'script' => $script->id )); ?>"><?php echo CHtml::encode($script->package->name); ?> <?php echo $script->package->version; ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $script->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'check/scripts', 'params' => array('id' => $category->id, 'control' => $control->id, 'check' => $check->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No scripts yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
