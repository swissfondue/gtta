<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            <?php if ($check->automated): ?>
                <li><a href="<?php echo $this->createUrl('check/scripts', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Scripts'); ?></a></li>
            <?php endif; ?>
            <li><a href="<?php echo $this->createUrl('check/results', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Results'); ?></a></li>
            <li class="active"><a href="<?php echo $this->createUrl('check/solutions', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Solutions'); ?></a></li>
            <li><a href="<?php echo $this->createUrl("check/share", array("id" => $category->id, "control" => $control->id, "check" => $check->id)); ?>"><?php echo Yii::t('app', "Share"); ?></a></li>
        </ul>
    </div>
    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('check/editsolution', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Solution'); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($solutions) > 0): ?>
                <table class="table solution-list">
                    <tbody>
                        <tr>
                            <th class="solution"><?php echo Yii::t('app', 'Solution'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($solutions as $solution): ?>
                            <tr data-id="<?php echo $solution->id; ?>" data-control-url="<?php echo $this->createUrl('check/controlsolution'); ?>">
                                <td class="solution">
                                    <a href="<?php echo $this->createUrl('check/editsolution', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'solution' => $solution->id )); ?>"><?php echo CHtml::encode($solution->localizedTitle); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $solution->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'check/solutions', 'params' => array('id' => $category->id, 'control' => $control->id, 'check' => $check->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No solutions yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
