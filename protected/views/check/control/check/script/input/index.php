<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl('check/editscript', array('id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'script' => $script->id)); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            <li class="active"><a href="<?php echo $this->createUrl('check/inputs', array('id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'script' => $script->id)); ?>"><?php echo Yii::t('app', 'Inputs'); ?></a></li>
        </ul>
    </div>
    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('check/editinput', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'script' => $script->id )) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Input'); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($inputs) > 0): ?>
                <table class="table input-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Input'); ?></th>
                            <th class="type"><?php echo Yii::t('app', 'Type'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($inputs as $input): ?>
                            <tr data-id="<?php echo $input->id; ?>" data-control-url="<?php echo $this->createUrl('check/controlinput'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('check/editinput', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'input' => $input->id, 'script' => $script->id )); ?>"><?php echo CHtml::encode($input->localizedName); ?></a>
                                </td>
                                <td class="type">
                                    <?php echo CHtml::encode($types[$input->type]); ?>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $input->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'check/inputs', 'params' => array('id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'script' => $script->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No inputs yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
