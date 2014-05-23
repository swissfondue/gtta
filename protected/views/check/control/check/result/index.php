<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            <?php if ($check->automated): ?>
                <li><a href="<?php echo $this->createUrl('check/scripts', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Scripts'); ?></a></li>
            <?php endif; ?>
            <li class="active"><a href="<?php echo $this->createUrl('check/results', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Results'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('check/solutions', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Solutions'); ?></a></li>
            <li><a href="<?php echo $this->createUrl("check/share", array("id" => $category->id, "control" => $control->id, "check" => $check->id)); ?>"><?php echo Yii::t('app', "Share"); ?></a></li>
        </ul>
    </div>
    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('check/editresult', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Result'); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($results) > 0): ?>
                <table class="table result-list">
                    <tbody>
                        <tr>
                            <th class="result"><?php echo Yii::t('app', 'Result'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($results as $result): ?>
                            <tr data-id="<?php echo $result->id; ?>" data-control-url="<?php echo $this->createUrl('check/controlresult'); ?>">
                                <td class="result">
                                    <a href="<?php echo $this->createUrl('check/editresult', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'result' => $result->id )); ?>"><?php echo CHtml::encode($result->localizedTitle); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $result->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'check/results', 'params' => array('id' => $category->id, 'control' => $control->id, 'check' => $check->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No results yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
