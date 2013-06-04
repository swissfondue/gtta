<div class="pull-right">
    <a class="btn" href="<?php echo $this->createUrl('reporttemplate/edit') ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Template'); ?></a>
</div>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($templates) > 0): ?>
                <table class="table risk-template-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Template'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($templates as $template): ?>
                            <tr data-id="<?php echo $template->id; ?>" data-control-url="<?php echo $this->createUrl('reporttemplate/control'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('reporttemplate/edit', array( 'id' => $template->id )); ?>"><?php echo CHtml::encode($template->localizedName); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $template->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'reporttemplate/index', 'params' => array())); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No templates yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
