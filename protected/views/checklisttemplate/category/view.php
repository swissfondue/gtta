<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl("checklisttemplate/viewcategory", array("id" => $category->id)); ?>"><?php echo Yii::t("app", "View"); ?></a></li>
            <li><a href="<?php echo $this->createUrl("checklisttemplate/editcategory", array("id" => $category->id)); ?>"><?php echo Yii::t("app", "Edit"); ?></a></li>
        </ul>
    </div>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('checklisttemplate/edittemplate', array( 'id' => $category->id )) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Template'); ?></a>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($templates) > 0): ?>
                <table class="table check-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Template'); ?></th>
                            <th class="check-count"><?php echo Yii::t('app', 'Checks'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($templates as $template): ?>
                            <tr data-id="<?php echo $template->id; ?>" data-control-url="<?php echo $this->createUrl('checklisttemplate/controltemplate', array( 'id' => $category->id, 'template' => $template->id )); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('checklisttemplate/viewtemplate', array( 'id' => $category->id, 'template' => $template->id )); ?>"><?php echo CHtml::encode($template->localizedName); ?></a>
                                </td>
                                <td><?php echo $template->checkCount; ?></td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $template->id; ?>, '<?php echo Yii::t('app', 'WARNING! ALL CHECKS WITHIN THIS CONTROL WILL BE DELETED!'); ?>');"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'check/view', 'params' => array('id' => $category->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No templates yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
